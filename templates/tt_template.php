<?php get_header(); ?>
<div id="imperial_page_title">
<h1 class="entry-title"><?php the_title(); ?></h1>
</div>
<main id="content" tabindex="-1">
<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
<div class="entry-content">



<?php

$site_url = get_site_url();

echo imperialThemeDraw::drawBackButton("Back to opportunities", $site_url).'<br/>';

// GEt the post meta
$post_id = get_the_ID();
$item_meta = get_post_meta($post_id);
$item_keys = tt_opps::get_meta_items();
foreach ($item_keys as $this_key)
{
    $$this_key = ''; // Defualt set to blank
    if(array_key_exists($this_key, $item_meta) )
    {
        $$this_key = $item_meta[$this_key][0];

    }
}

$role_type_img = z_taxonomy_image_url($role_type);


$event_type_name = get_term( $event_type )->name;
$role_type_name = get_term( $role_type )->name;
$module = get_term( $module )->name;
$cohort = get_term( $cohort )->name;

// Get the hex value of the background
$this_background = get_option('event_color_'.$event_type);

// Get the faded colour
$faded_color = tt_opps_utils::adjust_brightness($this_background, 50);



echo '<div class="tt_item_title_page" style="background:#'.$this_background.'">';


echo '<div class="role_type_img">';
if($role_type_img)
{
    echo '<img src="'.$role_type_img.'">';
}
echo '</div>';

echo '<div>';
echo '<h2>'.$event_type_name.' : '.$role_type_name.'</h2>';

if($primary_contact)
{
    echo 'Primary Contact : ';
    if($primary_contact_email)
    {
        echo '<a href="mailto:'.$primary_contact_email.'">';
    }
    echo $primary_contact;
    if($primary_contact_email)
    {
        echo '</a>';
    }

}
echo '</div>';
echo '</div>'; // end of top title box


$faded_inline_style = ' style="background:'.$faded_color.';"';

echo '<div class="opp_meta_wrap">';
echo '<div'.$faded_inline_style.'>';
echo '<h3>What is it?</h3>';
the_content();
echo '</div>';
echo '<div'.$faded_inline_style.'>';
echo '<h3>Who do we need?</h3>';
echo imperialNetworkUtils::convertTextFromDB($who);
echo '</div>';
echo '</div>';

echo '<div class="opp_meta_wrap">';
echo '<div'.$faded_inline_style.'>';
echo '<h3>What do I get out of it?</h3>';
echo $renumeration;
echo '</div>';

echo '<div'.$faded_inline_style.'>';
echo '<h3>Who are the students?</h3>';
echo 'Cohort : '.$cohort.'<br/>';
echo 'Module : '.$module.'<br/>';
echo '</div>';
echo '</div>';

echo '<div class="opp_meta_wrap">';
echo '<div'.$faded_inline_style.'>';
echo '<h3>How will I be supported?</h3>';
echo imperialNetworkUtils::convertTextFromDB($support);
echo '</div>';

echo '<div'.$faded_inline_style.'>';
echo '<h3>What will I need to do?</h3>';
echo imperialNetworkUtils::convertTextFromDB($tasks);
echo '</div>';
echo '</div>';


echo '<div class="opp_meta_wrap" id="tt_listener_wrap"><div>';
echo '<h3>Available Dates</h3>';
// Also show the dates
// GEt the post meta
$opp_dates = teaching_tinder_queries::get_opp_dates($post_id);

// Get all the dates this user has signed up for as well
$logged_in_username = imperialNetworkUtils::get_current_username();
$my_interest_dates = teaching_tinder_queries::get_user_interest($logged_in_username);

$date_count=0;
$now = date('Y-m-d H:i:s');

$dates_table =  '<table class="imperial-table" width="90%">';
$dates_table.= '<tr><th>Date</th><th>Available Spaces</th><th>Location</th><th>Status</th><th></th></tr>';
foreach($opp_dates as $date_meta)
{
    $date_id  = $date_meta->id;
    $location  = $date_meta->location;
    $start_date  = $date_meta->start_date;
    $end_date  = $date_meta->end_date;
    $status  = $date_meta->status;
    $visibility  = $date_meta->visibility;
    $spaces  = $date_meta->spaces;

    if($visibility==0)
    {
        continue;
    }

    if($now>$start_date)
    {
        continue;
    }

    switch ($status)
    {
        case "1":
            $status_str = '<span class="successText">Recruiting</span>';
        break;

        case "0":
            $status_str = '<span class="failText">Full</span>';
        break;
    }

    // have they signed up?
    $interest_status = 0;
    if(in_array($date_id, $my_interest_dates) )
    {
        $interest_status = 1;
    }

    $start_date_obj = new DateTime($start_date);
    $end_date_obj = new DateTime($end_date);
    $opp_date_str = $start_date_obj->format("l jS F, Y");
    $start_time = $start_date_obj->format("g:i a");
    $end_time = $end_date_obj->format("g:i a");
    $dates_table.= '<tr>';
    $dates_table.= '<td>'.$opp_date_str.'<br/><span class="smallText">'.$start_time.' - '.$end_time.'</span></td>';
    $dates_table.='<td>'.$spaces.'</td>';
    $dates_table.='<td>'.get_term( $location )->name.'</td>';
    $dates_table.='<td>'.$status_str.'</td>';
    $dates_table.= '<td>';

    $dates_table.= '<div id="interest_button_wrap_'.$date_id.'">';
    $dates_table.= teaching_tinder_draw::draw_interest_button($date_id, $interest_status);
    $dates_table.='</div>';
    $dates_table.='</td>';
    $dates_table.= '</tr>';

    $date_count++;
}

$dates_table.= '</table>';

if($date_count>=1)
{
    echo $dates_table;

}
else
{
    echo 'No dates found';
}

echo '</div></div>';



?>
</div>
</article>
<?php endwhile; endif; ?>
<?php edit_post_link($editPageIcon. 'Edit this oportunity', '<br/><br/>', '',  '', 'editPageButton'); ?>
</main>
<?php get_sidebar(); ?>
<?php get_footer(); ?>
