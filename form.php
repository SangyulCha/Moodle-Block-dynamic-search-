<?php
require_once("$CFG->libdir/formslib.php");
class block_google_search_form extends moodleform {
    function definition() {
        $mform = $this->_form;
        $search_array = array();
        $search_array[] = $mform->createElement('text', 'search_term');
        $mform->setType('search_term', PARAM_TEXT);
        $search_array[] = $mform->createElement('submit', 'submit', get_string('search', 'block_google_dynamic_search'));
        $mform->addGroup($search_array, 'searchar', 'Enter your search term',' ',false);
    }
}