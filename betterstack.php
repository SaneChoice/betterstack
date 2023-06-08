<?php
/**
 * @wordpress-plugin
 * Plugin Name: Better Stack Monitor Status
 * Description: Display the status of website monitors using the Better Uptime API.
 * Version: 1.1
 * Author: SaneChoice Limited
 * Author URI: https://www.sanechoice.cloud
 * Requires at least: 6.0
 * Requires PHP: 7.4
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

register_activation_hook(__FILE__, "wp_php_version_check");
function wp_php_version_check()
{
    // Set minimum required versions
    $min_wp_version = "6.0";
    $min_php_version = "7.4";

    // Get current versions
    global $wp_version;
    $current_php_version = phpversion();

    // Compare versions
    if (
        version_compare($wp_version, $min_wp_version, "<") ||
        version_compare($current_php_version, $min_php_version, "<")
    ) {
        // Deactivate the plugin
        deactivate_plugins(plugin_basename(__FILE__));

        // Show an error message
        wp_die(
            sprintf(
                __(
                    'This plugin requires at least WordPress %1$s and PHP %2$s. You are running WordPress %3$s and PHP %4$s. Please upgrade and try again.',
                    "wp-php-version-check"
                ),
                $min_wp_version,
                $min_php_version,
                $wp_version,
                $current_php_version
            )
        );
    }
}

register_activation_hook(__FILE__, "better_stock_monitor_status_table");
function better_stock_monitor_status_table()
{
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();
    $table_name = $wpdb->prefix . "better_stock_monitor_status_credentials";
    $sql = "CREATE TABLE `$table_name` (
   `id` int(11) NOT NULL AUTO_INCREMENT,
   `pronounceable_names` text DEFAULT '',
   `api_key` varchar(220) DEFAULT '',
   PRIMARY KEY(id)
   ) ENGINE=MyISAM DEFAULT CHARSET=latin1;
   ";
    if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
        require_once ABSPATH . "wp-admin/includes/upgrade.php";
        dbDelta($sql);
    }
}

add_action(
    "admin_menu",
    "add_admin_Better_stock_monitor_status_credential_form_page"
);
function add_admin_Better_stock_monitor_status_credential_form_page()
{
    add_menu_page(
        "BetterStack Monitor Status Credential",
        "BetterStack Monitor Status Credential",
        "manage_options",
        "better_stock_monitor_status_credential",
        "crud_admin_better_stock_monitor_status_credential_form_page",
        "dashicons-wordpress"
    );
}

function crud_admin_better_stock_monitor_status_credential_form_page()
{
    global $wpdb;
    $table_name = $wpdb->prefix . "better_stock_monitor_status_credentials";
    if (isset($_POST["newsubmit"])) {
        $pronounceable_names = $_POST["pronounceable_names"];
        $api_key = $_POST["api_key"];
        $wpdb->query(
            $wpdb->prepare(
                "INSERT INTO $table_name(pronounceable_names,api_key) VALUES(%s,%s)", [$pronounceable_names, $api_key]
            )
        );
        echo "<script>location.replace('admin.php?page=better_stock_monitor_status_credential');</script>";
    }
    if (isset($_POST["uptsubmit"])) {
        $id = $_POST["uptid"];
        $pronounceable_names = $_POST["pronounceable_names"];
        $api_key = $_POST["api_key"];
        $wpdb->query(
            $wpdb->prepare(
                "UPDATE $table_name SET pronounceable_names=%s,api_key=%s WHERE id=%d", [$pronounceable_names, $api_key, $id],
            )
        );
        echo "<script>location.replace('admin.php?page=better_stock_monitor_status_credential');</script>";
    }
    ?>
    <div class="wrap">
        <h2>BetterStack Monitor Status Credential</h2>
        <table class="wp-list-table widefat striped">
            <thead>
            <tr>
                <th width="60%">Pronounceable Names</th>
                <th width="25%">API Key</th>
                <th width="15%">Actions</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $result = $wpdb->get_results("SELECT * FROM $table_name");
            if (empty($result)) { ?>
                <form action="" method="post">
                    <tr>
                        <td><input type="text" id="pronounceable_names" name="pronounceable_names"
                                   placeholder="comma separated pronounceable names eg SaneChoice Website,SaneChoice POP3 etc"
                                   style='width: 100%;'>
                        </td>
                        <td><input type="text" id="api_key" name="api_key"></td>
                        <td>
                            <button id="newsubmit" name="newsubmit" type="submit">INSERT</button>
                        </td>
                    </tr>
                </form>
            <?php }
            foreach ($result as $print) {
                echo "
               <tr>
                 <td>$print->pronounceable_names</td>
                 <td>$print->api_key</td>
                 <td><a href='admin.php?page=better_stock_monitor_status_credential&upt=$print->id'><button type='button'>UPDATE</button></a></td>
               </tr>
             ";
            }
            ?>
            </tbody>
        </table>
        <br>
        <br>
        <?php if (isset($_GET["upt"])) {
            $upt_id = $_GET["upt"];
            $result = $wpdb->get_results(
                $wpdb->prepare(
                    "SELECT * FROM $table_name WHERE id=%d", [$upt_id]
                )
            );
            foreach ($result as $print) {
                $id = $print->id;
                $pronounceable_names = $print->pronounceable_names;
                $api_key = $print->api_key;
            }
            if (isset($id) && isset($pronounceable_names) && isset($api_key)) {
                echo "
         <table class='wp-list-table widefat striped'>
           <thead>
             <tr>
               <th width='60%'>Pronounceable Names</th>
               <th width='25%'>API key</th>
               <th width='15%'>Actions</th>
             </tr>
           </thead>
           <tbody>
             <form action='' method='post'>
               <tr>
                 <td>
                     <input type='hidden' id='uptid' name='uptid' value='$id'>
                     <input type='text' id='pronounceable_names' name='pronounceable_names' value='$pronounceable_names' placeholder='comma separated pronounceable names eg SaneChoice Website,SaneChoice POP3 etc' style='width: 100%;'>
                 </td>
                 <td><input type='text' id='api_key' name='api_key' value='$api_key'></td>
                 <td><button id='uptsubmit' name='uptsubmit' type='submit'>UPDATE</button> <a href='admin.php?page=better_stock_monitor_status_credential'><button type='button'>CANCEL</button></a></td>
               </tr>
             </form>
           </tbody>
         </table>";
            }
        } ?>
    </div>
    <?php
}

function betteruptime_api_request()
{
    global $wpdb;
    $table_name = $wpdb->prefix . "better_stock_monitor_status_credentials";
    $result = $wpdb->get_results("SELECT * FROM $table_name limit 1");
    foreach ($result as $print) {
        $pronounceable_names = array_map(
            "trim",
            explode(",", $print->pronounceable_names)
        );
        $api_key = $print->api_key;
    }

    if (!isset($pronounceable_names) || !isset($api_key)) {
        return [];
    }

    $url = "https://betteruptime.com/api/v2/monitors";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer $api_key",
        "Content-Type: application/json",
    ]);

    $result = curl_exec($ch);

    if (curl_errno($ch)) {
        return "Error:" . curl_error($ch);
    }

    curl_close($ch);

    return filterArrayResponse(
        json_decode($result, true),
        $pronounceable_names
    );
}

function filterArrayResponse($result, $pronounceable_names)
{
    $data = [];
    foreach ($pronounceable_names as $pronounceable_name) {
        foreach ($result["data"] as $item) {
            if (
                $item["attributes"]["pronounceable_name"] == $pronounceable_name
            ) {
                $data[] = $item;
            }
        }
    }

    $result["data"] = $data;

    return $result;
}

function betteruptime_monitor_status_shortcode()
{
    $response = betteruptime_api_request();

    if (isset($response["error"])) {
        return "Error: " . $response["error"]["message"];
    }

    $output = '<table class="betteruptime">';

    foreach ($response["data"] as $monitor) {
        $status =
            $monitor["attributes"]["status"] === "up"
                ? '<div style="color:MediumSeaGreen;">Operational</div>'
                : '<div style="color:blue;">Issues Found</div><br><a href="https://status.sanechoice.cloud/">Visit Dashboard</a>';
        $output .=
            "<tr><td>" .
            $monitor["attributes"]["pronounceable_name"] .
            "</td><td>" .
            $status .
            "</td></tr>";
    }

    $output .= "</table>";

    return $output;
}

add_shortcode(
    "betteruptime_monitor_status",
    "betteruptime_monitor_status_shortcode"
);

function betteruptime_monitor_status_enqueue()
{
    wp_enqueue_script("jquery");
}

add_action("wp_enqueue_scripts", "betteruptime_monitor_status_enqueue");

function betteruptime_monitor_status_refresh()
{
    ?>
    <script>
        jQuery(document).ready(function ($) {
            function refreshBetterUptimeMonitorStatus() {
                $.ajax({
                    url: '<?php echo admin_url("admin-ajax.php"); ?>',
                    data: {
                        action: 'betteruptime_monitor_status_refresh'
                    },
                    success: function (data) {
                        $('.betteruptime-monitor-status').replaceWith(data);
                    }
                });
            }

            setInterval(refreshBetterUptimeMonitorStatus, 5 * 60 * 1000);
        });
    </script>
    <?php
}

add_action("wp_footer", "betteruptime_monitor_status_refresh");

function betteruptime_monitor_status_ajax()
{
    echo betteruptime_monitor_status_shortcode();
    wp_die();
}

add_action(
    "wp_ajax_betteruptime_monitor_status_refresh",
    "betteruptime_monitor_status_ajax"
);
add_action(
    "wp_ajax_nopriv_betteruptime_monitor_status_refresh",
    "betteruptime_monitor_status_ajax"
);

function betteruptime_create_new_incident_refresh()
{
    ?>
    <script>
        jQuery(document).ready(function ($) {
            $('form#betteruptime_new_incident_form').on('submit', function (e) {
                e.preventDefault();

                let requester_email = $('#requester_email').val();
                let name = $('#name').val();
                let summary = $('#summary').val();
                let description = $('#description').val();

                if (!requester_email) {
                    alert('Requester email is mandatory');
                }

                if (!name) {
                    alert('Name is mandatory');
                }

                if (!summary) {
                    alert('Summary is mandatory');
                }

                if (!description) {
                    alert('Description is mandatory');
                }

                if (!requester_email || !name || !summary || !description) {
                    return;
                }

                $.ajax({
                    url: '<?= admin_url("admin-ajax.php") ?>',
                    type: 'post',
                    data: {
                        action: 'betteruptime_create_new_incident_refresh',
                        requester_email: requester_email,
                        name: name,
                        summary: summary,
                        description: description,
                    },
                    success: function (data) {
                        if (data === 'success') {
                            window.location.href = 'https://www.sanechoice.cloud/incsuccess/';
                        } else {
                            window.location.href = 'https://www.sanechoice.cloud/incfailure/';
                        }
                    },
                    error: function () {
                        window.location.href = 'https://www.sanechoice.cloud/incfailure/';
                    }
                });
            });
        });
    </script>
    <?php
}

add_action("wp_footer", "betteruptime_create_new_incident_refresh");

function betteruptime_create_new_incident_ajax()
{
    global $wpdb;
    $table_name = $wpdb->prefix . "better_stock_monitor_status_credentials";
    $result = $wpdb->get_results("SELECT * FROM $table_name limit 1");
    foreach ($result as $print) {
        $api_key = $print->api_key;
    }

    if (!isset($api_key)) {
        echo 'failed';
        wp_die();
    }

    $url = "https://uptime.betterstack.com/api/v2/incidents";

    $postParameter = [
        'requester_email' => $_POST['requester_email'] ?? '',
        'name' => $_POST['name'] ?? '',
        'summary' => $_POST['summary'] ?? '',
        'description' => $_POST['description'] ?? '',
        'call' => false,
        'sms' => true,
        'email' => true,
        'push' => true,
        'team_wait' => 60,//will wait 60s
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postParameter));
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer $api_key",
        "Content-Type: application/json",
    ]);

    curl_exec($ch);
    if (curl_errno($ch)) {
        echo "failed";
        wp_die();
    }
    curl_close($ch);

    echo "success";
    wp_die();
}

add_action(
    "wp_ajax_betteruptime_create_new_incident_refresh",
    "betteruptime_create_new_incident_ajax"
);
add_action(
    "wp_ajax_nopriv_betteruptime_create_new_incident_refresh",
    "betteruptime_create_new_incident_ajax"
);
