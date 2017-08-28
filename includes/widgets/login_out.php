<?php
/*
 * Name: FreeLand v2
 * Author: Marko Murumaa
 * Created: July 2015
 * Website: www.freelandplay.eu
*/
?>
			<div id="menu">
				<form action="p.php?p=login" method="POST">
					<ul>
						<li><img src="css/default/images/icons/user.png" width="16" height="16">Kasutajanimi:</li>
						<li><input type="text" name="username" placeholder="Kasutajanimi" autocomplete="off"></li>
						<li><img src="css/default/images/icons/register.png" width="16" height="16">Parool:</li>
						<li><input type="password" name="password" placeholder="Parool" autocomplete="off"></li>
						<li valign="center"><img src="css/default/images/icons/world.png" width="16" height="16">Maailm: <input type="radio" name="world" value="1">1 <input type="radio" name="world" value="2">2</li>
						<li>
							<input type="hidden" name="token" value="<?php echo Token::generate('LOGIN'); ?>">
							<input type="submit" value="Logi sisse">
						</li>
						<li><a href="p.php?p=newpassword">Unustasid parooli?</a></li>
					</ul>
				</form>
			</div>