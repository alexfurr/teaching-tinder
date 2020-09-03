<h1>Settings</h1>


<?php

echo teaching_tinder_draw::draw_feedback();



echo '<form method="post" action="edit.php?post_type=tt_opp&page=tt-settings&action=save-tt-settings">';
$event_types = teaching_tinder_queries::get_cat_items('tt_event_type');

foreach ($event_types as $term_id => $term_name)
{

    $this_option_name = "event_color_".$term_id;
    $term_color = get_option( $this_option_name );

    if($term_color=="")
    {
        $term_color = 'fff';
    }

    $postfix = '<span style="background:#'.$term_color.'; padding:5px;">Example</span>';

    $args = array(
        "type" => "textbox",
        "name" => $this_option_name,
        "value" => $term_color,
        "ID" => $this_option_name,
        "label" => $term_name.' color',
        "width" => 200,
        "prefix" => "#",
        "postfix" => $postfix,
    );

    echo ek_forms::form_item($args);

}


$args = array(
    "type" => "submit",
    "name" => 'submit',
    "value" => 'Save settings',
    "ID" => 'submit',
    "class" => 'button-primary',
);

echo ek_forms::form_item($args);

echo '</form>';

?>
