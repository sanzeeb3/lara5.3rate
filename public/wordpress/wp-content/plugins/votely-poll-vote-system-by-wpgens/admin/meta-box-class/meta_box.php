<?php

/**
 * recives data about a form field and spits out the proper html
 *
 * @param   array                   $field          array with various bits of information about the field
 * @param   string|int|bool|array   $meta           the saved data for this field
 * @param   array                   $repeatable     if is this for a repeatable field, contains parant id and the current integar
 *
 * @return  string                                  html for the field
 */
function votely_meta_box_field($field, $meta = null, $repeatable = null) {
    if (!( $field || is_array($field) ))
        return;

    // type    - text | tel | email | url | number | textarea | checkbox | select | radio
    // label   - meta title 
    // desc    - goes below input box
    // context - where the meta box appear: normal (default), advanced, side; optional
    // options - array for select boxes
    $type = isset($field['type']) ? $field['type'] : null;
    $label = isset($field['label']) ? $field['label'] : null;
    $desc = isset($field['desc']) ? '<span class="description">' . $field['desc'] . '</span>' : null;
    $context = isset($field['context']) ? $field['context'] : null;
    $options = isset($field['options']) ? $field['options'] : null;

    // the id and name for each field
    $id = $name = isset($field['id']) ? $field['id'] : null;
    switch ($type) {
        // basic
        case 'text':
        case 'tel':
        case 'email':
        default:
            echo '<input type="' . $type . '" name="' . esc_attr($name) . '" id="' . esc_attr($id) . '" value="' . esc_attr($meta) . '" class="regular-text"/>';
            break;
        case 'url':
            echo '<input type="' . $type . '" name="' . esc_attr($name) . '" id="' . esc_attr($id) . '" value="' . esc_url($meta) . '" class="regular-text"/>';
            break;
        case 'number':
            echo '<input type="' . $type . '" name="' . esc_attr($name) . '" id="' . esc_attr($id) . '" value="' . intval($meta) . '" class="regular-text"/>';
            break;
        // textarea
        case 'textarea':
            echo '<textarea name="' . esc_attr($name) . '" id="' . esc_attr($id) . '" cols="60" rows="4">' . esc_textarea($meta) . '</textarea>';
            break;
        // checkbox
        case 'checkbox':
            echo '<input type="checkbox" name="' . esc_attr($name) . '" id="' . esc_attr($id) . '" ' . checked($meta, true, false) . ' value="1" />
                    <label for="' . esc_attr($id) . '">' . $desc . '</label>';
            break;
        // select, chosen
        case 'select':
            echo '<select name="' . esc_attr($name) . '" id="' . esc_attr($id) . '">
                    <option value="">Select One</option>'; // Select One
            foreach ($options as $option)
                echo '<option' . selected($meta, $option['value'], false) . ' value="' . $option['value'] . '">' . $option['label'] . '</option>';
            echo '</select>';
            break;
        // radio
        case 'radio':
            echo '<ul class="meta_box_items">';
            foreach ($options as $option)
                echo '<li><input type="radio" name="' . esc_attr($name) . '" id="' . esc_attr($id) . '-' . $option['value'] . '" value="' . $option['value'] . '" ' . checked($meta, $option['value'], false) . ' />
                        <label for="' . esc_attr($id) . '-' . $option['value'] . '">' . $option['label'] . '</label></li>';
            echo '</ul>';
            break;
    } //end switch
}

/**
 * Finds any item in any level of an array
 *
 * @param   string  $needle     field type to look for
 * @param   array   $haystack   an array to search the type in
 *
 * @return  bool                whether or not the type is in the provided array
 */
function votely_box_find_field_type($needle, $haystack) {
    foreach ($haystack as $h)
        if (isset($h['type']) && $h['type'] == 'repeatable')
            return votely_box_find_field_type($needle, $h['repeatable_fields']);
        elseif (( isset($h['type']) && $h['type'] == $needle ) || ( isset($h['repeatable_type']) && $h['repeatable_type'] == $needle ))
            return true;
    return false;
}

/**
 * sanitize boolean inputs
 */
function votely_box_santitize_boolean($string) {
    if (!isset($string) || $string != 1 || $string != true)
        return false;
    else
        return true;
}

/**
 * outputs properly sanitized data
 *
 * @param   string  $string     the string to run through a validation function
 * @param   string  $function   the validation function
 *
 * @return                      a validated string
 */
function votely_box_sanitize($string, $function = 'sanitize_text_field') {
    switch ($function) {
        case 'intval':
            return intval($string);
        case 'absint':
            return absint($string);
        case 'wp_kses_post':
            return wp_kses_post($string);
        case 'wp_kses_data':
            return wp_kses_data($string);
        case 'esc_url_raw':
            return esc_url_raw($string);
        case 'is_email':
            return is_email($string);
        case 'sanitize_title':
            return sanitize_title($string);
        case 'santitize_boolean':
            return santitize_boolean($string);
        case 'sanitize_text_field':
        default:
            return sanitize_text_field($string);
    }
}

/**
 * Map a multideminsional array
 *
 * @param   string  $func       the function to map
 * @param   array   $meta       a multidimensional array
 * @param   array   $sanitizer  a matching multidimensional array of sanitizers
 *
 * @return  array               new array, fully mapped with the provided arrays
 */
function votely_box_array_map_r($func, $meta, $sanitizer) {

    $newMeta = array();
    $meta = array_values($meta);

    foreach ($meta as $key => $array) {
        if ($array == '')
            continue;
        /**
         * some values are stored as array, we only want multidimensional ones
         */
        if (!is_array($array)) {
            return array_map($func, $meta, (array) $sanitizer);
            break;
        }
        /**
         * the sanitizer will have all of the fields, but the item may only 
         * have valeus for a few, remove the ones we don't have from the santizer
         */
        $keys = array_keys($array);
        $newSanitizer = $sanitizer;
        if (is_array($sanitizer)) {
            foreach ($newSanitizer as $sanitizerKey => $value)
                if (!in_array($sanitizerKey, $keys))
                    unset($newSanitizer[$sanitizerKey]);
        }
        /**
         * run the function as deep as the array goes
         */
        foreach ($array as $arrayKey => $arrayValue)
            if (is_array($arrayValue))
                $array[$arrayKey] = votely_box_array_map_r($func, $arrayValue, $newSanitizer[$arrayKey]);

        $array = array_map($func, $array, $newSanitizer);
        $newMeta[$key] = array_combine($keys, array_values($array));
    }
    return $newMeta;
}

/**
 * takes in a few peices of data and creates a custom meta box
 * Example: $sample_box = new custom_add_meta_box( 'team_meta', 'Position and Social Links', $fields, array('post','team' ), false, 'normal' );
 *
 * @param string        $id        meta box id
 * @param string        $title     title
 * @param array         $fields    array of each field the box should include
 * @param string|array  $page      post type to add meta box to
 * @param bool          $js        enque javascript or not
 * @param string        $context   The part of the page where the edit screen section should be shown ('normal', 'advanced', or 'side')
 */
class Votely_Add_Meta_Box {

    var $id;
    var $title;
    var $fields;
    var $page;
    var $js;
    var $context;

    public function __construct($id, $title, $fields, $page, $js, $context) {
        $this->id = $id;
        $this->title = $title;
        $this->fields = $fields;
        $this->page = $page;
        $this->js = $js;
        $this->context = $context;
        if (!is_array($this->page))
            $this->page = array($this->page);

        add_action('add_meta_boxes', array($this, 'add_box'));
        add_action('save_post', array($this, 'save_box'));
    }

    /**
     * adds the meta box for every post type in $page
     */
    function add_box() {
        foreach ($this->page as $page) {
            add_meta_box($this->id, $this->title, array($this, 'meta_box_callback'), $page, $this->context, 'low');
        }
    }

    /**
     * outputs the meta box
     */
    function meta_box_callback() {

        // Use nonce for verification
        wp_nonce_field('lt_meta_box_nonce_action', 'lt_meta_box_nonce_field');

        // Begin the field table and loop
        echo '<table class="form-table meta_box">';
        foreach ($this->fields as $field) {
            if ($field['type'] == 'section') {
                echo '<tr>
                        <td colspan="2">
                            <h2>' . $field['label'] . '</h2>
                        </td>
                    </tr>';
            } else {
                echo '<tr class="' .$field['id'].'">
                        <th><label for="' . $field['id'] . '">' . $field['label'] . '</label></th>
                        <td>';

                $meta = get_post_meta(get_the_ID(), $field['id'], true);
                echo votely_meta_box_field($field, $meta);

                echo '</td><td>' . $field['desc'] . '</td></tr>';
            }
        } // end foreach
        echo '</table>'; // end table
    }

    /**
     * saves the captured data
     */
    function save_box($post_id) {
        $post_type = get_post_type();

        // verify nonce
        if (!isset($_POST['lt_meta_box_nonce_field']))
            return $post_id;
        if (!( in_array($post_type, $this->page) || wp_verify_nonce($_POST['lt_meta_box_nonce_field'], 'lt_meta_box_nonce_action') ))
            return $post_id;
        // check autosave
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
            return $post_id;
        // check permissions
        if (!current_user_can('edit_page', $post_id))
            return $post_id;

        // loop through fields and save the data
        foreach ($this->fields as $field) {
            if ($field['type'] == 'section') {
                $sanitizer = null;
                continue;
            } else {
                // save the rest
                $new = false;
                $old = get_post_meta($post_id, $field['id'], true);
                if (isset($_POST[$field['id']]))
                    $new = $_POST[$field['id']];
                if (isset($new) && '' == $new && $old) {
                    delete_post_meta($post_id, $field['id'], $old);
                } elseif (isset($new) && $new != $old) {
                    $sanitizer = isset($field['sanitizer']) ? $field['sanitizer'] : 'sanitize_text_field';
                    if (is_array($new))
                        $new = votely_box_array_map_r('votely_box_sanitize', $new, $sanitizer);
                    else
                        $new = votely_box_sanitize($new, $sanitizer);
                    update_post_meta($post_id, $field['id'], $new);
                }
            }
        } // end foreach
    }

}
