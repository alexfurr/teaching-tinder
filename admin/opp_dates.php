<?php
$opp_id = $_GET['opp_id'];
$title = get_the_title($opp_id);


echo '<h1>'.$title.' : Available Dates</h1>';
echo imperialThemeDraw::drawBackButton("Back to opportunities", "edit.php?post_type=tt_opp");


echo teaching_tinder_draw::draw_feedback();

echo '<button class="button-secondary" id="show_form_button">Add a new date</button>';
echo '<div id="add_dates_div" style="display:none; background:white; border:1px solid #666; padding:10px; margin:5px 0px;">';
echo '<form method="post" action="?page=tt-opp-dates&opp_id='.$opp_id.'&action=add_tt_date">'.


$opp_date = '';
$location = '';
$start_time = '';
$end_time = '';
$spaces='';

// Add dates
$args = array(
    "type" => "date",
    "name" => "opp_date",
    "value" => $opp_date,
    "ID" => "opp_date",
    "label" => "Date",
);
echo ek_forms::form_item($args);

$args = array(
    "type" => "time",
    "name" => "start_time",
    "value" => $start_time,
    "ID" => "start_time",
    "label" => "Start Time",
);
echo ek_forms::form_item($args);

$args = array(
    "type" => "time",
    "name" => "end_time",
    "value" => $end_time,
    "ID" => "end_time",
    "label" => "End Time",
);
echo ek_forms::form_item($args);

$locations = teaching_tinder_queries::get_cat_items('tt_location');

$args = array(
    "type" => "dropdown",
    "name" => "location",
    "value" => $location,
    "ID" => "location",
    "label" => "Location",
    "options" => $locations,
);
echo ek_forms::form_item($args);

$args = array(
    "type" => "dropdown",
    "name" => "spaces",
    "value" => $spaces,
    "ID" => "spaces",
    "label" => "Available Spaces",
    "options" => array(1,2,3,4,5,6),
);
echo ek_forms::form_item($args);

echo '<input type="hidden" value="'.$opp_id.'" name="opp_id" />';

echo '<input type="submit" value="Submit" class="button-primary"/>';
echo '</form>';
echo '</div>';

// GEt the post meta
$opp_dates = teaching_tinder_queries::get_opp_dates($opp_id);

$date_count = count($opp_dates);
if($date_count==0)
{
    echo '<h4>No dates found</h4>';
}
else
{

    echo '<form method="post" action="?page=tt-opp-dates&opp_id='.$opp_id.'&action=bulk_action_opp_dates&feedback=dates_deleted">';

    echo '<table class="imperial-table" width="90%" id="dates_table">';
    echo '<tr>';
    echo '<th width="2px"><label>';
    echo '<input type="checkbox" id="checkall" value="1" onclick="javascript:checkCheckboxes(this.id);" ></label></th>';
    echo '<th>Date</th><th>Spaces</th><th>Interest</th><th>Location</th><th>Status</th><th>Visibility</th></tr>';
    foreach($opp_dates as $date_meta)
    {
        $date_id  = $date_meta->id;
        $location  = $date_meta->location;
        $start_date  = $date_meta->start_date;
        $end_date  = $date_meta->end_date;
        $status  = $date_meta->status;
        $visibility  = $date_meta->visibility;
        $spaces  = $date_meta->spaces;

        $date_obj = new DateTime($start_date);
        $opp_date_str = $date_obj->format("l jS F, Y, g:i a");

        // Also get the list of users who have expressed interest

        $my_users = teaching_tinder_queries::get_users_by_opp_date($date_id);

        $user_count = count($my_users);

        switch ($status)
        {

            case "0":
                $status_str = '<span class="failText">Waiting List</span>';
            break;

            default:
                $status_str = '<span class="successText">Recruiting</span>';
            break;
        }

        switch ($visibility)
        {
            case "0":
                $visibilty_str = '<span class="failText">Hidden</span>';
            break;

            default:
                $visibilty_str = '<span class="successText">Visible</span>';
            break;
        }

        echo '<tr>';
        echo '<td width="10px" valign="top"><input type="checkbox" name="check_list[]" value="'.$date_id.'" id="delete_'.$date_id.'"></td>';
        echo '<td>';
        echo '<label for="delete_'.$date_id.'">'.$opp_date_str.'</label></td>';
        echo '<td>'.$spaces.'</td>';
        echo '<td>';

        echo $user_count.' people';
        if($user_count>=1)
        {
            echo '<br/><a href="" class="smallText toggle_users_click" data-id="'.$date_id.'">Show people</a>';

            echo '<div id="user_list_'.$date_id.'" class="smallText" style="display:none;">';
            foreach ($my_users as $this_user)
            {
                $username = $this_user->username;
                $user_meta = imperialQueries::getUserInfo($username);
                $this_name = $user_meta['first_name'].' '.$user_meta['last_name'];
                $this_email = $user_meta['email'];

                echo $this_name.' (<a href="mailto:'.$this_email.'">'.$this_email.')</a><br/>';
            }
            echo '</div>';

        }
        echo '</td>';



        echo '<td>'.$locations[$location].'</td>';
        echo '<td>'.$status_str.'</td>';
        echo '<td>'.$visibilty_str.'</td>';

        echo '</tr>';

    }

    echo '</table>';


    $bulk_actions = array(
        "delete" => "Delete",
        "status_recruiting" => "Change Status to recruiting",
        "status_waitlist" => "Change Status to waitlist",
        "vis_shown" => "Change vis to shown",
        "vis_hidden" => "Change vis to hidden",
    );


    $bulk_button = '<input type="submit" value="Apply bulk action" name="bulk_button" class="button-secondary"/>';
    $args = array(
        "type" => "dropdown",
        "name" => "bulk_actions",
        "ID" => "bulk_actions",
        "label" => "With Selected",
        "options" => $bulk_actions,
        "postfix"   => $bulk_button,
    );

    echo ek_forms::form_item($args);


}

echo '</form>';


?>

<script>

jQuery( document ).ready(function() {

    jQuery( "#show_form_button" ).click(function() {
      jQuery( "#add_dates_div" ).slideToggle("fast");
    });

    jQuery( ".toggle_users_click" ).click(function(event) {
        var target_id = jQuery( this ).attr('data-id');
        console.log(target_id);

        jQuery( "#user_list_"+target_id ).slideToggle("fast");
        event.preventDefault();


    });





});

// Checks / unchecks all checkboxes in a div (pID) given other checkbox ID (id)
function checkCheckboxes( id ){

    jQuery('#dates_table').find(':checkbox').each(function(){

        jQuery(this).attr('checked', jQuery('#' + id).is(':checked'));

    });

}
</script>
