<?php
// This file is part of Moodle - https://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Block google_search is defined here.
 *
 * @package     block_google_dynamic_search
 * @copyright   2024 sangyul cha <eddie6798@gmail.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once(dirname(__FILE__) . '/form.php');

class block_google_dynamic_search extends block_base
{
    const PLUGIN_NAME = 'block_google_dynamic_search';

    /**
     * Initializes class member variables.
     */
    public function init()
    {
        // Needed by Moodle to differentiate between blocks.
        $this->title = get_string('pluginname', self::PLUGIN_NAME);
    }

    /**
     * Returns the block contents.
     *
     * @return stdClass The block contents.
     */
    public function get_content()
    {

        if ($this->content !== null) {
            return $this->content;
        }

        if (empty($this->instance)) {
            $this->content = '';
            return $this->content;
        }

        $this->content = new stdClass();
        // Google API Key and Custom Search Engine ID
        $api_key = get_config(self::PLUGIN_NAME, 'apikey');
        $search_engine_id = get_config(self::PLUGIN_NAME, 'search_engine_id');
        // Display form
        $form = new block_google_search_form();

        // Check if the form is cancelled
        if ($form->is_cancelled()) {
            // If cancelled, clear the content and return
            $this->content->text = '';
            return $this->content;
        }

        // Get form data
        if (!$data = $form->get_data()) {
            // If form data is not available, display the form and return
            $this->content->text = $form->render();
            return $this->content;
        }

        // Process form submission
        $search_term = str_replace(' ', '%20', $data->search_term);

        // Check if search term is provided
        if (empty($search_term)) {
            // If search term is empty, display the form and return
            $this->content->text = $form->render();
            return $this->content;
        }

        // Retrieve search results
        $search_results = $this->get_search_results($search_term, $search_engine_id, $api_key);

        // Display search results
        $results_html = $this->display_search_results($search_results);
        $form_html = $form->render() . $results_html;
        $this->content->text = $form_html;
        return $this->content;
    }

    private function get_search_results($search_term, $search_engine_id, $api_key)
    {
        // Google API call
        $api_url = "https://www.googleapis.com/customsearch/v1?q=$search_term&key=$api_key&cx=$search_engine_id";
        $response = file_get_contents($api_url);

        // Convert JSON data to an associative array
        return json_decode($response, true);
    }

    private function display_search_results($results)
    {
        $html_result = '<div class="block-google-dynamic-search">';
        $html_result .= '<link rel="stylesheet" type="text/css" href="' . $this->get_css_url() . '">';
        $html_result .= '<table>';
        foreach ($results['items'] as $item) {
            $title = $item['title'];
            $link = $item['link'];
            $html_result .= "<tr><td><a href='$link'>$title</a></td></tr>";
        }
        $html_result .= '</table>';
        $html_result .= '</div>';

        return $html_result;
    }

    private function get_css_url()
    {
        $block_path = rtrim(dirname(__FILE__), '/');
        $css_file = 'style.css';
        return "$block_path/$css_file";
    }

    /**
     * Defines configuration data.
     *
     * The function is called immediately after init().
     */
    public function specialization()
    {

        // Load user defined title and make sure it's never empty.
        if (empty($this->config->title)) {
            $this->title = get_string('pluginname', self::PLUGIN_NAME);
        } else {
            $this->title = $this->config->title;
        }
    }

    /**
     * Sets the applicable formats for the block.
     *
     * @return string[] Array of pages and permissions.
     */
    public function applicable_formats()
    {
        return array(
            'all' => true,
        );
    }

    public function instance_allow_multiple()
    {
        return true;
    }

    public function has_config()
    {
        return true;
    }

    function _self_test()
    {
        return true;
    }
}
