<?php
require_once("$CFG->libdir/formslib.php");
class block_google_search_form extends moodleform {
    function definition() {
        $mform = $this->_form;
        $mform->addElement('text', 'search_term', get_string('search_field', 'block_google_dynamic_search'));
        $mform->setType('search_term', PARAM_TEXT);
        $mform->addElement('submit', 'submit', get_string('search', 'block_google_dynamic_search'));
    }
}