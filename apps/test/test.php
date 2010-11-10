<?php
class Test extends Application
{
    function testit() {
        $this->set_var('testvar', 'Test it!');
    }

    function testbernd() {
        Debug::out("Hallo Bernd!");
    }

    function nomaintpl() {
        $this->set_no_general_template();
    }

    function notpl() {
        $this->set_no_template();
        echo "Blahh!";
    }

    function testajax() {
        $this->set_no_general_template();
        $this->set_no_template();
        echo "Foobar\nis\ncool!";
    }

    function testplugin() {
        $plugin_content = $this->plug('test', 'testplugincontent');
        echo "Just a test! Hahah ;-)";
        $this->set_var('testpluginvar', $plugin_content);
        $this->set_var('normalvar', 'Just a normal Variable');
    }

    function testplugincontent() {
        $this->set_no_general_template();
        $this->set_no_template();
        echo "From Plugin Method ...";
    }

    function testdb() {
        $this->set_no_general_template();
        $this->set_no_template();
        FW::add_header('Content-Type', 'text/plain');
        $db = DB::get_connection(Config::db_driver, Config::db_host, Config::db_username, Config::db_password, Config::db_name);
        $res = $db->query("SELECT * FROM languages");
        $rows = $db->get_rows($res);
        foreach($rows as $row) {
            echo json_encode($row) . "\n";
        }
        $db->close();
    }

    function get_allowed_methods() {
        return array(
            'testit',
            'notpl',
            'nomaintpl',
            'testajax',
            'testplugin',
            'testdb'
        );
    }
}
?>