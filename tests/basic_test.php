<?php
/**
 * @author Jeremy Blanchard (auzigog) <auzigog@gmail.com>
 * @author Marcin Mieteń <marcin4444@gmail.com>
 *
 */
class TestOfGoogleGroupsAPI extends WebTestCase {
  var $gg;
  var $last_email_added;
  var $random_email;

  var $login_email;
  var $login_password;

  var $group_shortname;
  var $group_url;

  const DUMMY_MEMBER = 'ggphpapi+dummy@gmail.com';

  function __construct() {
    require_once(dirname(__FILE__) .'/../google-groups-php-api.php');

    // Should set the $login_email, $login_password, and $test_group
    require_once(dirname(__FILE__) .'/test_config.php');
    $this->login_email = $login_email;
    $this->login_password = $login_password;
    $this->group_shortname = $test_group;

    $this->group_url = 'http://groups.google.com/group/'. $this->group_shortname;

    $this->gg = new GoogleGroupsAPI();
    $this->gg->setGroup($this->group_shortname);
    $this->random_email = $this->_getRandomEmail();
  }

  /**
   * Must run this before doing assertions so the test bowser is logged in.
   *
   * @todo Probably a way to fix this so the setting only has to be done one time
   * isntead of after every change in the browser within the GG API.
   */
  function _fixBrowser() {
    $b = $this->gg->getBrowser();
    $this->setBrowser($b);
  }

  function _getRandomEmail() {
    return 'ggphpapi+'. mt_rand(1000, 9999) .'@gmail.com';
  }

  function testLogin() {
    $this->gg->login($this->login_email, $this->login_password);

    $this->_fixBrowser();

    $page = $this->get($this->group_url .'/members_invite');
    //echo $page; die('here');
    $this->assertText('Enter email addresses of people to invite', 'Succesfully logged in with owner or manager access.');
  }

  function testInvite() {
    $this->gg->memberInvite($this->random_email);

    $this->_fixBrowser();

    $page = $this->get($this->group_url .'/manage_members?view=invite');
    //echo $page; die();
    $this->assertText($this->random_email, 'Member successfully invited! Email: '. $this->random_email);
  }

  function testUnsubscribe() {
    $this->gg->memberUnsubscribe($this->random_email);

    $this->_fixBrowser();

    $page = $this->get($this->group_url .'/manage_members');
    //echo $page; die();
    $this->assertNoText($this->random_email, 'Member successfully removed! Email: '. $this->random_email);
  }
}