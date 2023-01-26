<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

namespace dvc\cal;

use currentUser;
use dvc\ews\calendaritem;
use Json;
use strings;

class controller extends \Controller {
  protected $feeds = [];  // unless you populate this it won't work ...

  protected function _index() {

    $start = date('Y-m-d');
    $end = date('Y-m-d', strtotime('+7 days'));
    $this->data = (object)[
      'start' => $start,
      'end' => $end,
      'feed' => []
    ];

    $this->render([
      'primary' => 'calendar',
      'secondary' => 'feeds',
      'data' => (object)[
        'searchFocus' => false,
      ],
    ]);
  }

  protected function before() {

    parent::before();
    $this->viewPath[] = __DIR__ . '/views/';
  }

  protected function postHandler() {
    $action = $this->getPost('action');

    if ('appointment-save' == $action) {
      $date = $this->getPost('date');
      $start = $this->getPost('start');
      $end = $this->getPost('end');

      if (strtotime($date) > 0) {
        $start = $date . ' ' . $start;
        $end = $date . ' ' . $end;

        $app = new calendaritem;

        $app->subject = $this->getPost('subject');
        $app->notes = $this->getPost('notes');
        $app->location = $this->getPost('location');

        $app->startUTC = $start;
        $app->endUTC = $end;

        if (dav\appointment::create($app)) {
          Json::ack($action);
        } else {
          Json::nak($action);
        }
      } else {
        Json::nak($action);
      }
    } elseif ('get-active-feeds' == $action) {
      $a = [];
      foreach ($this->feeds as $feed) {
        if ('yes' == currentUser::option('cal-feed-' . $feed->name)) {
          $a[] = $feed;
        }
      }

      Json::ack($action)
        ->add('data', $a);
    } elseif ('get-feed' == $action) {
      $name = $this->getPost('name');
      $type = $this->getPost('type');
      // \sys::logger( sprintf('<feed %s> %s', $type, __METHOD__));

      if ('dav' == $type) {
        if ($account = $this->getPost('account')) {
          $settings = false;

          $config = implode(DIRECTORY_SEPARATOR, [
            config::dataPath(),
            sprintf('%s.json', $account)

          ]);

          if (file_exists($config)) {
            $settings = (array)json_decode(file_get_contents($config));
          } elseif ((int)$account) {
            $dao = new \dao\users;
            $settings = $dao->getCalDavCredentialsUserByID((int)$account);
          }

          if ($settings) {
            $client = new dav\client($settings);

            $calendars = [];
            $events = [];
            // printf( '<br>principal : %s', $client->principal);

            if ($calendar = $client->getCalendar('Personal')) {
              $start = $this->getPost('start');
              $end = $this->getPost('end');

              $_events = $client->getEvents($calendar, $start, $end);
              // \sys::logger( sprintf('<events %s - %s> <%s> %s', $start, $end, count( $_events), __METHOD__));
              // \sys::dump( $_events, 'Personal Calendar');

              $events = [];
              foreach ($_events as $_event) {
                $reader = reader::readICS($_event->data);
                $feed = $reader->feed();
                foreach ($feed as $e) {
                  $events[] = array_merge([
                    'uid' => $_event->uid,
                    'etag' => $_event->etag

                  ], $e);
                }
              }
            }

            Json::ack($action)
              ->add('data', $events);
            // \sys::logger( sprintf('<feed %s> %s', count( $events), __METHOD__));

          }
        } else {
          Json::nak(sprintf('invalid account - %s', $action));
        }
      } elseif ('Australian Public Holidays' == $name) {
        $path = implode(DIRECTORY_SEPARATOR, [
          __DIR__,
          'data',
          'australian_public_holidays.csv'

        ]);

        $start = $this->getPost('start');
        $end = $this->getPost('end');

        $reader = reader::readCSV($path);
        $feed = $reader->feed($start, $end);

        Json::ack($action)
          ->add('data', $feed);
      } elseif ('Queensland Public Holidays' == $name) {
        $path = implode(DIRECTORY_SEPARATOR, [
          __DIR__,
          'data',
          'australian_public_holidays.csv'

        ]);

        $start = $this->getPost('start');
        $end = $this->getPost('end');

        $reader = reader::readCSV($path);
        $feed = $reader->feed($start, $end, function ($evt) {
          return 'qld' == $evt['location'];
        });

        Json::ack($action)
          ->add('data', $feed);
      } else {
        Json::nak(sprintf('%s - %s', $name, $action));
      }
    } elseif ('toggle-feed' == $action) {
      if ($feed = $this->getPost('feed')) {
        $state = $this->getPost('state');

        currentUser::option('cal-feed-' . $feed, 'yes' == $state ? 'yes' : '');
        Json::ack($action);
      } else {
        Json::nak($action);
      }
    } else {
      parent::postHandler();
    }
  }

  public function agenda() {

    $seed = $this->getParam('seed');
    if (strtotime($seed) < 1) {
      $seed = date('Y-m-d');
    }

    $this->data = (object)[
      'seed' => $seed,
      'days' => 7
    ];

    $this->load('agenda');
  }

  public function widget() {
    $start = date('Y-m-d');
    $end = date('Y-m-d');
    $this->data = (object)[
      'start' => $start,
      'end' => $end,
      'feed' => [],
      'mode' => 'widget'

    ];

    $this->load('calendar');
  }

  public function widget_guts() {

    $seed = $this->getParam('seed');
    if (strtotime($seed) < 1) $seed = date('Y-m-d');

    $this->data = (object)[
      'seed' => $seed,
      'days' => 1

    ];

    $this->load('agenda');
  }

  public function appointment() {
    $this->data = (object)[
      'title' => $this->title = 'Appointment'

    ];

    $this->load('appointment');
  }

  public function month() {
    $seed = $this->getParam('seed');
    if (strtotime($seed) < 1) {
      $seed = date('Y-m-d');
    }

    $time = strtotime($seed);
    $seed = date('Y-m-01', $time);

    $this->data = (object)[
      'seed' => $seed

    ];

    $this->load('month');
  }

  public function week() {
    $seed = $this->getParam('seed');
    if (strtotime($seed) < 1) {
      $seed = date('Y-m-d');
    }

    $time = strtotime($seed);
    if (date('w', $time)) {
      $seed = date('Y-m-d', strtotime('Last Sunday', $time));
    }

    $this->data = (object)[
      'seed' => $seed

    ];

    $this->load('week');
  }
}
