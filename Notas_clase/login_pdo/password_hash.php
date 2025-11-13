<?php
	echo password_hash('123', PASSWORD_DEFAULT);
	// Genera un hash (contraseña encriptada) a partir del texto 'ana123'.
	// PASSWORD_DEFAULT usa el algoritmo más seguro disponible (actualmente bcrypt).
	// El resultado es un texto largo y encriptado que NO se puede revertir.
	// Este hash es el que debes guardar en la base de datos, nunca la contraseña original.
	// echo imprime ese hash en la pantalla para que puedas copiarlo o guardarlo.
?>
