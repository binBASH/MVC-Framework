<?php
class Home extends Application
{
    public function run() {
        // TODO: Load static sites out of CMS
        $this->set_var('cms_content', 'Welcome to our Page!');
    }

    public function get_allowed_methods() {
        return array(
            'run'
        );
    }
}
?>