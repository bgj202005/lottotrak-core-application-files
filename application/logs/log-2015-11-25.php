<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

ERROR - 2015-11-25 07:14:43 --> Severity: Error --> Call to undefined function generateSalt() C:\wamp2\Code_Igniter_Application_Files\application\models\user_m.php 53
ERROR - 2015-11-25 07:15:25 --> Severity: Notice --> Array to string conversion C:\wamp2\Code_Igniter_Application_Files\system\database\DB_query_builder.php 662
ERROR - 2015-11-25 07:15:25 --> Query error: Unknown column 'Array' in 'where clause' - Invalid query: SELECT *
FROM `users`
WHERE `email` = 'bgj20@hotmail.com'
AND `password` = '$2y$10$Onp8k1l6g7aLqAvQDDQPq.fU8Rme8fZJ7wYtpPnUVqLC/nWQc3TLu'
AND 0 = `Array`
ORDER BY `id`
ERROR - 2015-11-25 07:15:25 --> Query error: Unknown column 'email' in 'where clause' - Invalid query: UPDATE `ci_sessions` SET `timestamp` = 1448432125
WHERE `email` = 'bgj20@hotmail.com'
AND `password` = '$2y$10$Onp8k1l6g7aLqAvQDDQPq.fU8Rme8fZJ7wYtpPnUVqLC/nWQc3TLu'
AND 0 = `Array`
AND `id` = 'd7d4a4fcb8c8710a9a392e94ba7e529c6ab8e2fd'
ORDER BY `id`
ERROR - 2015-11-25 07:17:41 --> Severity: Notice --> Array to string conversion C:\wamp2\Code_Igniter_Application_Files\system\database\DB_query_builder.php 662
ERROR - 2015-11-25 07:17:41 --> Query error: Unknown column 'Array' in 'where clause' - Invalid query: SELECT *
FROM `users`
WHERE `email` = 'bgj20@hotmail.com'
AND `password` = '$2y$10$kwfUCKpBWTedY4B.BwPwpep6VZ/GSazF9jl.VJe76jX8./Ths37Qq'
AND 0 = `Array`
ORDER BY `id`
ERROR - 2015-11-25 07:17:41 --> Query error: Unknown column 'email' in 'where clause' - Invalid query: UPDATE `ci_sessions` SET `timestamp` = 1448432261
WHERE `email` = 'bgj20@hotmail.com'
AND `password` = '$2y$10$kwfUCKpBWTedY4B.BwPwpep6VZ/GSazF9jl.VJe76jX8./Ths37Qq'
AND 0 = `Array`
AND `id` = 'd7d4a4fcb8c8710a9a392e94ba7e529c6ab8e2fd'
ORDER BY `id`
