<?php
/*
 * Name: FreeLand v2
 * Author: Marko Murumaa
 * Created: July 2015
 * Website: www.freelandplay.eu
*/

$_lir = true;
require_once("core/init.php");

$fight_turns = 10;
$fight_max_money = 150000000;

if(Input::exists()) {
  if(Token::check(Input::get('token'), 'OPEN_FIGHT') ) {
    $value = round(Input::get('value'));
    $cur_fight_count_query = DB::getInstance()->query("SELECT * FROM `fight_requests` WHERE `type` = 2 AND `user_id` = " . $_USER->data()->id);

    if ($value < 1 or empty($value) === true) {
      $_GENERAL->addError("Palun kirjutage kui suure panuse te teete.");
    }

    if ($cur_fight_count_query->count() > 0) {
      $_GENERAL->addError("Te olete juba avaliku panuse teinud.");
    }

    if ($_USER->data('data')->money < $value) {
      $_GENERAL->addError("Teil ei ole piisavalt sularaha.");
    }

    if ($_USER->data('data')->turns < $fight_turns) {
      $_GENERAL->addError("Teil ei ole piisavalt käike.");
    }

    if ($value > $fight_max_money) {
      $_GENERAL->addError("Maksimaalne panus on ".$_GENERAL->format_number($fight_max_money).".");
    }

    if (empty($_GENERAL->errors()) === true) {
      $_USER->update(array(
          'money' => $_USER->data('data')->money - $value,
          'turns' => $_USER->data('data')->turns - $fight_turns
        ),$_USER->data()->id, 'users_data');

      $public_fight_fields = array(
        'type' => 2,
        'user_id' => $_USER->data()->id,
        'money' => $value
        );
      DB::getInstance()->insert('fight_requests', $public_fight_fields);

      Session::flash('streetfight', 'Teie avalik väljakutse on tehtud.');
      Redirect::to('p.php?p=streetfight');
    }
  } else if(Token::check(Input::get('token'), 'TAKE_MONEY') ) {

    if (empty($_USER->data('data')->fight_fond) === true) {
      $_GENERAL->addError("Teie avalik fond on tühi.");
    }

    if (empty($_GENERAL->errors()) === true) {
      $_USER->update(array(
          'money' => $_USER->data('data')->money + $_USER->data('data')->fight_fond,
          'fight_fond' => 0
        ),$_USER->data()->id, 'users_data');

      Session::flash('streetfight', 'Te võtsite edukalt raha välja.');
      Redirect::to('p.php?p=streetfight');
    }
  }
}

if(Input::exists('get')) {
  if (empty(Input::get('public')) === false) {
    $fight_query = DB::getInstance()->query("SELECT * FROM `fight_requests` WHERE `id` = " . (int)Input::get('public') . " AND `status` = 0");
    if (!$fight_query->count()) {
      $_GENERAL->addError("Sellist väljakutset ei leitud.");
    } else {
      $fight_data = $fight_query->first();

      if ($_USER->data('data')->id == $fight_data->user_id) {
        $_GENERAL->addError("See on teie avalik väljakutse.");
      }

      if ($_USER->data('data')->money < $fight_data->money) {
        $_GENERAL->addError("Teil ei ole piisavalt sularaha.");
      }

      if (empty($_GENERAL->errors()) === true) {
        $enemy_data_query = DB::getInstance()->get('users_data', array('id','=',$fight_data->user_id));
        $enemy_data = $enemy_data_query->first();

        $user_points = $_USER->data('data')->speed + $_USER->data('data')->strength + $_USER->data('data')->stamina;
        $enemy_points = $enemy_data->speed + $enemy_data->strength + $enemy_data->stamina;

        if ($user_points == $enemy_points) {
          $user_points = $user_points + mt_rand(0, 10);
          $enemy_points = $enemy_points + mt_rand(0, 10);
        }

        if ($user_points > $enemy_points) {
          $_USER->update(array(
              'money' => $_USER->data('data')->money + $fight_data->money
            ),$_USER->data()->id, 'users_data');

          DB::getInstance()->delete('fight_requests', array('id', '=', $fight_data->id));
          Session::flash('streetfight', 'Te võitsite.');
        } else {
          $_USER->update(array(
              'money' => $_USER->data('data')->money - $fight_data->money
            ),$_USER->data()->id, 'users_data');

          DB::getInstance()->update('users_data', $enemy_data->id, array('fight_fond' => $enemy_data->fight_fond + ($fight_data->money * 2) ));

          DB::getInstance()->delete('fight_requests', array('id', '=', $fight_data->id));
          Session::flash('streetfight_lose', 'Te kaotasite.');
        }

        Redirect::to('p.php?p=streetfight');
      }
    }
  } else if (empty(Input::get('fight')) === false) {
    $fight_query = DB::getInstance()->query("SELECT * FROM `fight_requests` WHERE `id` = " . (int)Input::get('fight') . " AND `o_user_id` = ".$_USER->data()->id." AND `status` = 0");
    if (!$fight_query->count()) {
      $_GENERAL->addError("Sellist väljakutset ei leitud.");
    } else {
      $fight_data = $fight_query->first();

      if ($_USER->data('data')->money < $fight_data->money) {
        $_GENERAL->addError("Teil ei ole piisavalt sularaha.");
      }

      if (empty($_GENERAL->errors()) === true) {
        $enemy_data_query = DB::getInstance()->get('users_data', array('id','=',$fight_data->user_id));
        $enemy_data = $enemy_data_query->first();

        $user_points = $_USER->data('data')->speed + $_USER->data('data')->strength + $_USER->data('data')->stamina;
        $enemy_points = $enemy_data->speed + $enemy_data->strength + $enemy_data->stamina;

        if ($user_points == $enemy_points) {
          $user_points = $user_points + mt_rand(0, 10);
          $enemy_points = $enemy_points + mt_rand(0, 10);
        }

        if ($user_points > $enemy_points) {
          $_USER->update(array(
              'money' => $_USER->data('data')->money + $fight_data->money
            ),$_USER->data()->id, 'users_data');

          DB::getInstance()->update('fight_requests', $fight_data->id, array('status' => 3));

          Session::flash('streetfight', 'Te võitsite.');
        } else {
          $_USER->update(array(
              'money' => $_USER->data('data')->money - $fight_data->money
            ),$_USER->data()->id, 'users_data');

          DB::getInstance()->update('fight_requests', $fight_data->id, array('status' => 2));

          Session::flash('streetfight_lose', 'Te kaotasite.');
        }

        Redirect::to('p.php?p=streetfight');
      }
    }
  } else if (empty(Input::get('cancel')) === false) {
    $fight_query = DB::getInstance()->query("SELECT * FROM `fight_requests` WHERE `id` = " . (int)Input::get('cancel') . " AND `o_user_id` = ".$_USER->data()->id." AND `status` = 0");
    if (!$fight_query->count()) {
      $_GENERAL->addError("Sellist väljakutset ei leitud.");
    } else {
      $fight_data = $fight_query->first();

      if (empty($_GENERAL->errors()) === true) {
        DB::getInstance()->update('fight_requests', $fight_data->id, array('status' => 1));
        Redirect::to('p.php?p=streetfight');
      }
    }
  } else if (empty(Input::get('abort')) === false) {
    $fight_query = DB::getInstance()->query("SELECT * FROM `fight_requests` WHERE `id` = " . (int)Input::get('abort') . " AND `user_id` = ".$_USER->data()->id." AND `status` = 0");
    if (!$fight_query->count()) {
      $_GENERAL->addError("Sellist väljakutset ei leitud.");
    } else {
      $fight_data = $fight_query->first();

      if (empty($_GENERAL->errors()) === true) {
        $_USER->update(array(
            'money' => $_USER->data('data')->money + $fight_data->money
          ),$_USER->data()->id, 'users_data');

        DB::getInstance()->delete('fight_requests', array('id', '=', $fight_data->id));
        Redirect::to('p.php?p=streetfight');
      }
    }
  } else if (empty(Input::get('take')) === false) {
    $fight_query = DB::getInstance()->query("SELECT * FROM `fight_requests` WHERE `id` = " . (int)Input::get('take') . " AND `user_id` = ".$_USER->data()->id." AND `status` = 1 OR `status` = 2");
    if (!$fight_query->count()) {
      $_GENERAL->addError("Sellist väljakutset ei leitud.");
    } else {
      $fight_data = $fight_query->first();

      if (empty($_GENERAL->errors()) === true) {
        $_USER->update(array(
            'money' => $_USER->data('data')->money + $fight_data->money
          ),$_USER->data()->id, 'users_data');

        DB::getInstance()->delete('fight_requests', array('id', '=', $fight_data->id));
        Redirect::to('p.php?p=streetfight');
      }
    }
  } else if (empty(Input::get('delete')) === false) {
    $fight_query = DB::getInstance()->query("SELECT * FROM `fight_requests` WHERE `id` = " . (int)Input::get('delete') . " AND `user_id` = ".$_USER->data()->id." AND `status` = 3");
    if (!$fight_query->count()) {
      $_GENERAL->addError("Sellist väljakutset ei leitud.");
    } else {
      $fight_data = $fight_query->first();

      if (empty($_GENERAL->errors()) === true) {
        DB::getInstance()->delete('fight_requests', array('id', '=', $fight_data->id));
        Redirect::to('p.php?p=streetfight');
      }
    }
  }
}



include("includes/overall/header.php");
?>

<div id="page">
  <div class="page-title">Tänava kaklus</div>
  <p>
  <?php
  if (empty($_GENERAL->errors()) === false) {
    print($_GENERAL->output_errors());
  }

  if(Session::exists('streetfight_lose')) {
    $_GENERAL->addError(Session::flash('streetfight_lose'));
    print($_GENERAL->output_errors());
  }

  if(Session::exists('streetfight')) {
    $_GENERAL->addOutSuccess(Session::flash('streetfight'));
    print($_GENERAL->output_success());
  }
  ?>
    <table>
      <tr valign="top">
        <td width="20%"><img src="css/default/images/fight.png" width="100" height="100"></td>
        <td width="80%">
          Tänava kakluses on võimalik kakelda teiste mängijatega raha peale.<br>
          Kakluse esitamine võtab <?php print($_GENERAL->format_number($fight_turns));?> käiku ja maksimaalne panus kakluses on <?php print($_GENERAL->format_number($fight_max_money));?>.
        </td>
      </tr>
    </table>
  </p>
</div>
<?php
$fight_public_query = DB::getInstance()->query("SELECT * FROM `fight_requests` WHERE `type` = 2 AND `status` = 0");
foreach ($fight_public_query->results() as $public_fight) {
  if ($public_fight->user_id == $_USER->data()->id) {
    $link = '<a href="p.php?p=streetfight&abort='.$public_fight->id.'">Tühista</a>';
  } else {
    $link = '<a href="p.php?p=streetfight&public='.$public_fight->id.'">Võta avalik väljakutse vastu</a>';
  }

  $output_line_public .= '
    <tr>
      <td align="center">'.$_GENERAL->format_number($public_fight->money).'</td>
      <td align="center">'.$public_fight->date.'</td>
      <td align="center">'.$link.'</td>
    </tr>
  ';
}
?>
<div id="page">
  <div class="page-title">Avalikud väljakutsed</div>
  <p>
    <table>
      <form action="p.php?p=streetfight" method="POST">
        <tr>
          <td width="30%">Avalikus fondis raha:</td>
          <td width="70%">
            <?php print($_GENERAL->format_number($_USER->data('data')->fight_fond));?>
            <input type="hidden" name="token" value="<?php echo Token::generate('TAKE_MONEY'); ?>">
            <input type="submit" value="Tühjenda fond">
          </td>
        </tr>
      </form>
      <tr>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
      </tr>
      <form action="p.php?p=streetfight" method="POST">
        <tr>
          <td>Esita avaliku kakluse kutse:</td>
          <td>
            <input type="text" name="value" placeholder="Kaklemise panus" autocomplete="off">
            <input type="hidden" name="token" value="<?php echo Token::generate('OPEN_FIGHT'); ?>">
            <input type="submit" value="Esita avalik kaklus">
          </td>
        </tr>
      </form>
    </table>
    <table>
      <tr>
        <th width="35%">Summa</th>
        <th width="25%">Esitamise aeg</th>
        <th width="40%">Otsus</th>
      </tr>
      <?php
        print($output_line_public);
      ?>
    </table>
    <?php 
      if (empty($output_line_public) === true) {
        $_GENERAL->addOutInfo("Hetkel ei ole ühtegi avalikku väljakutset.");
        print("<br>");
        print($_GENERAL->output_info());
      }
    ?>
  </p>
</div>
<?php
$fight_to_query = DB::getInstance()->query("SELECT * FROM `fight_requests` WHERE `type` = 1 AND `o_user_id` = ".$_USER->data()->id." AND `status` = 0");
foreach ($fight_to_query->results() as $to_fight) {
  $to_username_query = DB::getInstance()->query("SELECT `username` FROM `users` WHERE `id` = " . $to_fight->user_id);
    $to_username = $to_username_query->first()->username;

  $link = '<a href="p.php?p=streetfight&fight='.$to_fight->id.'">Võta vastu</a> | <a href="p.php?p=streetfight&cancel='.$to_fight->id.'">Keeldu</a>';
  $output_line_to .= '
    <tr>
      <td><a href="p.php?p=profile&user='.$to_username.'">'.$to_username.'</a></td>
      <td align="center">'.$_GENERAL->format_number($to_fight->money).'</td>
      <td align="center">'.$to_fight->date.'</td>
      <td align="center">'.$link.'</td>
    </tr>
  ';
}
?>
<div id="page">
  <div class="page-title">Teile esitatud väljakutsed</div>
  <p>
    <table>
      <tr>
        <th width="25%">Kellelt</th>
        <th width="20%">Summa</th>
        <th width="25%">Esitamise aeg</th>
        <th width="30%">Otsus</th>
      </tr>
      <?php
      print($output_line_to);
      ?>
    </table>
    <?php 
      if (empty($output_line_to) === true) {
        $_GENERAL->addOutInfo("Teile ei ole esitatud ühtegi väljakutset.");
        print("<br>");
        print($_GENERAL->output_info());
      }
    ?>
  </p>
</div>
<?php
$fight_from_query = DB::getInstance()->query("SELECT * FROM `fight_requests` WHERE `type` = 1 AND `user_id` = " . $_USER->data()->id);
foreach ($fight_from_query->results() as $from_fight) {
  $from_username_query = DB::getInstance()->query("SELECT `username` FROM `users` WHERE `id` = " . $from_fight->o_user_id);
    $from_username = $from_username_query->first()->username;

  if ($from_fight->status == 0) {
    $link = '<a href="p.php?p=streetfight&abort='.$from_fight->id.'">Tühista</a>';
    $result = 'Ootab vastu võtmist';
  } else if ($from_fight->status == 1) {
    $link = '<a href="p.php?p=streetfight&take='.$from_fight->id.'">Võta raha</a>';
    $result = 'Vastane keeldus';
  } else if ($from_fight->status == 2) {
    $link = '<a href="p.php?p=streetfight&take='.$from_fight->id.'">Võta raha</a>';
    $result = 'Te võitsite';
  } else if ($from_fight->status == 3) {
    $link = '<a href="p.php?p=streetfight&delete='.$from_fight->id.'">Kustuta</a>';
    $result = 'Te kaotasite';
  }

  $output_line_from .= '
    <tr>
      <td><a href="p.php?p=profile&user='.$from_username.'">'.$from_username.'</a></td>
      <td align="center">'.$_GENERAL->format_number($from_fight->money).'</td>
      <td align="center">'.$from_fight->date.'</td>
      <td align="center">'.$result.'</td>
      <td align="center">'.$link.'</td>
    </tr>
  ';
}
?>
<div id="page">
  <div class="page-title">Teie esitatud väljakutsed</div>
  <p>
    <table>
      <tr>
        <th width="25%">Kellele</th>
        <th width="20%">Summa</th>
        <th width="20%">Esitamise aeg</th>
        <th width="20%">Tulemus</th>
        <th width="15%">Otsus</th>
      </tr>
      <?php
      print($output_line_from);
      ?>
    </table>
    <?php 
      if (empty($output_line_from) === true) {
        $_GENERAL->addOutInfo("Te ei ole esitanud ühtegi väljakutset.");
        print("<br>");
        print($_GENERAL->output_info());
      }
    ?>    
  </p>
</div>

<?php
include("includes/overall/footer.php");
