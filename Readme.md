# Moodle Course Policy
A collection of bodges to present the student with a EULA before being subscribed to a
specific module. This was required by the licensing of some content we had.

This is based on: https://moodle.org/mod/forum/discuss.php?d=32306 and I've been
hacking/bodging it around to keep it working on each successive Moodle upgrade
we've run through.


## Coursepolicy.php by Nathaniel J. Bird (April 2007) for AliveTek, Inc. www.alivetek.com.

* This is based on the policy.php 
* If an html file named coursepolicy#.php (# symbol replaced by an actual course id), then the page will be displayed 
    with a requirement to click "Yes" before being able to continue with the course.
* Modifications to `lib/moodlelib.php` and `lang/en/moodle.php` are required. 
* A new VARCHAR field named `coursepoliciesagreed` is required in the `mdl_user` table also. 


1. Add column coursepoliciesagreed VARCHAR(255) to mdl_user table

   This field is needed to save a string of course IDs

2. Add to lang/en/moodle.php :
```php
   $string['coursepolicyagree'] = 'You must agree to this policy to continue using this site.  Do you agree?';
   $string['coursepolicyagreement'] = 'Course Policy Agreement';
   $string['coursepolicyagreementclick'] = 'Click here to read the Course Policy Agreement';
```
3. Upload the coursepolicy.php to user directory

   The attached zip file contains the coursepolicy.php. Upload it to moodle/user/

4. Find in lib/moodlelib.php :
```php
	if (!$USER->policyagreed and !is_siteadmin()) {
        if (!empty($CFG->sitepolicy) and !isguestuser()) {
            if ($preventredirect) {
                throw new require_login_exception('Policy not agreed');
            }
            if ($setwantsurltome) {
                $SESSION->wantsurl = qualified_me();
            }
            redirect($CFG->wwwroot .'/user/policy.php');
        } else if (!empty($CFG->sitepolicyguest) and isguestuser()) {
            if ($preventredirect) {
                throw new require_login_exception('Policy not agreed');
            }
            if ($setwantsurltome) {
                $SESSION->wantsurl = qualified_me();
            }
            redirect($CFG->wwwroot .'/user/policy.php');
        }
    }
```
5. After that section, add this code to lib/moodlelib.php :
```php
    // Check that the user has agreed to the course policy if there is one
    if (file_exists($CFG->dataroot.'/1/coursepolicy'.$course->id.'.html')) {
        $coursepoliciesagreed = split(',',$USER->coursepoliciesagreed);
        // If the course id is not in coursepoliciesagreed, display the course policy
        if(!in_array($course->id,$coursepoliciesagreed)) {
            $SESSION->wantsurl = qualified_me();
            redirect($coursepolicypath = $CFG->wwwroot.'/user/coursepolicy.php?id='.$course->id);
        }
    }
```
6. If you are logged on as a test user, log OFF before trying again!
