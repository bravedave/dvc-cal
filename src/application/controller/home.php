<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

class home extends dvc\cal\controller {
	protected function before() {
    $this->feeds = config::cal_feeds();

  }

	protected function postHandler() {
    $action = $this->getPost('action');

    if ( 'get-feed' == $action) {
      $name = $this->getPost('name');

      if ( 'Dingo' == $name) {
        $path = implode( DIRECTORY_SEPARATOR, [
          dvc\cal\config::dataPath(),
          '3.json'

        ]);

        $start = $this->getPost('start');
        $end = $this->getPost('end');

        $reader = dvc\cal\reader::readJSON( $path);
        $feed = $reader->feed( $start, $end);

        Json::ack( $action)
          ->add( 'data', $feed);

      }
      elseif ( 'Sample Feed' == $name) {
        $path = implode( DIRECTORY_SEPARATOR, [
          config::dataPath(),
          'feed.ics'

        ]);

        $start = $this->getPost('start');
        $end = $this->getPost('end');

        // $reader = reader::readICS( $path);
        $reader = dvc\cal\reader::ICSString( file_get_contents( $path));
        $feed = $reader->feed( $start, $end);

        Json::ack( $action)
          ->add( 'data', $feed);

      }
      else {
        parent::postHandler();

      }

    }
    else {
      parent::postHandler();

    }

  }

  protected function page( $params) {
		$defaults = [
			'scripts' => [],

		];

    $options = array_merge( $defaults, $params);

    $options['scripts'][] = sprintf(
      '<script type="text/javascript" src="%s"></script>',
      strings::url('js/cal')

    );

    return parent::page( $options);

  }

}
