<?php

$ek_forms = new ek_forms();
class ek_forms
{


	//~~~~~
	function __construct ()
	{
		$this->addWPActions();
	}

/*	---------------------------
	PRIMARY HOOKS INTO WP
	--------------------------- */
	function addWPActions ()
	{
        add_action( 'wp_enqueue_scripts', array( $this, 'load_forms_scripts' ), 1 );
		add_action( 'admin_enqueue_scripts', array( $this, 'load_forms_scripts' ) );
	}

    // Loads the quote js for the quote calculator
    function load_forms_scripts()
    {
        wp_enqueue_script( 'jquery' );
        wp_enqueue_script( 'jquery-ui-datepicker' );

        // You need styling for the datepicker. For simplicity I've linked to the jQuery UI CSS on a CDN.
        wp_enqueue_style( 'jquery-ui', 'https://code.jquery.com/ui/1.12.1/themes/smoothness/jquery-ui.css' );

        wp_enqueue_style('ek_forms_css', plugins_url('/css/forms.css',__FILE__) );
    }


    // $args array example
    /*
        $args = array(
        "type" => "dropdown",
        "name" => "remote_teaching_area",
        "value" => $remote_teaching_area,
        "ID" => "remote_teaching_area",
        "label" => "Area",
        "options" => array("option1", "option2"),
        "use_raw_values"    => true,
        );
    */


    public static function form_item($args = array())
    {

        $html = '<div class="ek_form_item">';
        $value = '';
        $required = '';
        $class='';
        $label = '';
        $prefix = '';
        $postfix = '';

        $type = $args['type'];
        $ID = $args['ID'];

        // If no name is given the ID will be the name
        $name = $args['ID'];
        if(isset($args['name']) ){$name = $args['name'];}
        if(isset($args['label']) ){$label = $args['label'];}
        if(isset($args['value']) ){$value = $args['value'];}
        if(isset($args['required']) ){$required = $args['required'];}
        if(isset($args['width']) ){$width = $args['width'];}
        if(isset($args['class']) ){$class = $args['class'];}
        if(isset($args['prefix']) ){$prefix = $args['prefix'];}
        if(isset($args['postfix']) ){$postfix = $args['postfix'];}

        switch ($type)
        {

            case "checkbox":
            $html.='<label for="'.$ID.'">';
            $html.='<input class="'.$class.'" type="checkbox" name="'.$name.'" id="'.$ID.'"';
            if($value=="on"){ $html.=' checked ';}
            $html.='/>'.$label.'</label>';


            break;

            case "radio":
            $options = $args['options'];
            $use_raw_values = false;
            if(isset($args['use_raw_values']) ){$use_raw_values = $args['use_raw_values'];}

            $html.=$label;

            $html.='<div class="ek_radio_wrap">';
            foreach ($options as $option_key => $option_value)
            {


                // If there are no valid 'keys' then use the actual text as the values
                if($use_raw_values==true){
                    $option_key = imperialNetworkUtils::create_filename($option_value);
                }
                $html.='<label for="'.$option_key.'">';
                $html.='<input class="'.$class.'" type="radio" value= "'.$option_key.'" name="'.$name.'" id="'.$option_key.'"';
                if($value==$option_key){$html.= ' checked ';}
                $html.='>'.$option_value.'.</label>';

            }
            $html.='</div>';


            break;

            case "file_upload":
                $html.='<label for="'.$ID.'">'.$label.'</label>';
                $html.='<input class="'.$class.'" type="file" name="'.$name.'" id="'.$ID.'" '.$required.'>';
            break;


            case "dropdown":

                $options = $args['options'];
                $use_raw_values = false;
                if(isset($args['use_raw_values']) ){$use_raw_values = $args['use_raw_values'];}

                $html.='<label for="'.$ID.'">'.$label.'</label>';
                $html.='<select class="'.$class.'" name="'.$name.'" id="'.$ID.'">';
                $html.='<option value="">Please select</option>';

                foreach ($options as $option_key => $option_value)
                {

                    // If there are no valid 'keys' then use the actual text as the values
                    if($use_raw_values==true){
                        $option_key = imperialNetworkUtils::create_filename($option_value);
                    }

                    $html.='<option value="'.$option_key.'"';
                    if($option_key==$value){$html.=' selected ';}
                    $html.='>'.$option_value;
                    $html.='</option>';
                }

                $html.='</select>';
                $html.=$postfix;

            break;




            case "date":
                if($value==""){$value = date('Y-m-d');}
                if($value=="null"){$value='';} // allow it to be blank if required

                $html.= '<label for="'.$ID.'">'.$label.'</label>';
                $html.=  '<input type="text" name="'.$ID.'" id="'.$ID.'" value="'.$value.'"/>';
                $html.=  '<script>
                jQuery(function() {
                    jQuery( "#'.$ID.'" ).datepicker({
                        dateFormat : "yy-mm-dd"
                    });
                });
                </script>';
            break;

            case "datetime":

                if($value==""){$value = date('Y-m-d H:i');}


                $dt = \DateTime::createFromFormat("Y-m-d H:i", $value);
                $hour_value = $dt->format('H');
                $min_value = $dt->format('i');
                $date_value= $dt->format('Y-m-d');
                $html.= '<label for="'.$ID.'">'.$label.'</label>';
                $html.=  '<input type="text" name="'.$name.'" id="'.$ID.'" value="'.$date_value.'"/>';
                $html.=  '<script>
                jQuery(function() {
                    jQuery( "#'.$ID.'" ).datepicker({
                        dateFormat : "yy-mm-dd"
                    });
                });
                </script>';

                // Now add the time drop downs
                $html.='<div>';
                $html.='<label for="'.$ID.'_hour">Time</label>';
                $html.= ek_forms::draw_time_picker( $hour_value, $min_value, $ID."_hour", $ID."_min");
                $html.='</div>';



            break;

            case "time":
                if($value==""){
                    $hour_value  = '09';
                    $min_value  = '00';
                }
                else
                {
                    $temp_value = explode(':', $value);
                    $hour_value = $temp_value[0];
                    $min_value = $temp_value[1];
                }

                $html.='<div>';
                $html.='<label for="'.$ID.'_hour">'.$label.'</label>';
                $html.= ek_forms::draw_time_picker( $hour_value, $min_value, $ID."_hour", $ID."_min");
                $html.='</div>';
            break;

            case "textarea":
                // use Rich Text Editor?
                $RTE = false;
                if(isset($args['RTE']) )
                {
                    $RTE = $args['RTE'];
                }
                $html.='<label for="'.$ID.'">'.$label.'</label>';

                if($RTE==true)
                {
                    // Turn on the output buffer
                    ob_start();

                    // Echo the editor to the buffer
            		$editor_settings = array
            		(
            			"media_buttons"	=> true,
            			"editor_class"	=> "ek-textarea",
            			"textarea_rows"	=> 6,
            			"tinymce"		=> array(
            			'toolbar1'	=> 'bold,italic,underline,bullist,numlist,forecolor,undo,redo',
            			'toolbar2'	=> ''
            			)
            		);



                    $simple_editor = true;
                    if(isset($_GET['simple_editor']) )
                    {
                        $simple_editor = $_GET['simple_editor'];
                    }

            		if($simple_editor==true)
            		{
            		//	$editor_settings['tinymce']['toolbar1'] = 'undo,redo';	// ONLY add the undo and redo buttons
            		}

                    wp_editor($value, $ID, $editor_settings);
                    $html.= ob_get_clean();


                    // Store the contents of the buffer in a variable
                }
                else
                {
                    $html.='<textarea class="'.$class.'" id="'.$ID.'" name="'.$name.'">'.$value.'</textarea>';
                }


            break;

            case "submit":
                $html.='<input class="'.$class.'" type="submit" value="'.$value.'" id="'.$ID.'" />';
            break;

            case "textbox":
            default:
                $this_width = '';
                if(isset($width))
                {

                    $this_width='style="width:'.$width.'px;"';
                }
                $html.='<label for="'.$ID.'">'.$label.'</label>';
                $html.=$prefix.'<input id="'.$ID.'" name="'.$name.'" value="'.$value.'" '.$this_width.' type="textbox" '.$required.'>'.$postfix;
            break;



        }

        $html.='</div>';

        return $html;

    }

    public static function draw_time_picker($hourValue="", $minValue="",$hourID="myHour", $minID="myMin")
    {

        if($hourValue==""){$hourValue=10;}
        $i=1;
        $html = '';
        $html.='<select name="'.$hourID.'" id="'.$hourID.'">';
        while($i<=23)
        {
           $iString = $i;
           if($i<10){$iString = '0'.$i;}
           $html.='<option value="'.$iString.'"';
           if($hourValue==$iString){$html.= ' selected ';}
           $html.='>'.$iString.'</option>';
           $i++;
        }
        $html.='</select>';

        $i=0;
        $html.='<select name="'.$minID.'" id="'.$minID.'">';

        while($i<=59)
        {
           $iValue=$i;
           if($iValue<10){$iValue = '0'.$i;}
           $html.='<option value="'.$iValue.'"';
          if($minValue==$iValue){$html.= ' selected ';}

          $html.= '>'.$iValue.'</option>';
           $i = $i+5;
        }
        $html.='</select>';

        //$html.='<select name="'.$minID.'_AMPM" id="'.$minID.'_AMPM">';
        //$html.='<option value="AM">AM</option>';
        //$html.='<option value="PM">PM</option>';
        //$html.='</select>';

        return $html;

    }

    // Takes a raw value such as 'My Value' lowercases and adds hyphens suitable for CSS
    static function convert_str_to_css($input)
    {

        $output = strtolower($input);

        //Clean up multiple dashes or whitespaces
        $output = preg_replace("/[\s-]+/", " ", $output);
        //Convert whitespaces and underscore to dash
        $output = preg_replace("/[\s_]/", "-", $output);

        return $output;
    }

    // Takes a raw value such as 'my_value' and makes it more readble
    static function convert_raw_to_text($input)
    {

        $output = ucwords($input);

        $output = str_replace("_", " ", $output);

        return $output;
    }




}


?>
