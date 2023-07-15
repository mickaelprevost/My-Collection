-- Adminer 4.8.1 MySQL 5.5.5-10.3.38-MariaDB-0ubuntu0.20.04.1 dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

SET NAMES utf8mb4;

INSERT INTO `album` (`id`, `universe_id_id`, `user_id_id`, `title`, `description`, `poster`, `created_at`, `updated_at`) VALUES
(1,	1,	2,	'BowserCollection',	'edition deluxe ulitmate',	'bowser-64762b5abd909.jpg',	'2023-05-30 18:59:06',	NULL),
(2,	1,	2,	'marvelCollection',	'vive les marvel heroes',	'marvel2-6476394b06d89.jpg',	'2023-05-30 19:58:34',	NULL),
(3,	1,	3,	'figurines disney',	'j\'adore l\'univers disney, je viens de commencer une collection de figurines trop mims.',	'figurines-disney-64766adf22ba7.jpg',	'2023-05-30 23:07:08',	NULL),
(4,	1,	4,	'telephones oldschool',	'voici ma collection de vieux tel, cela ne nous rajeuni pas...',	'oldphones-64766b4d32052.jpg',	'2023-05-30 23:31:57',	NULL),
(5,	1,	5,	'ma petite collec de livres :)',	'je dois vous faire une confidence, je n\'ai pas tout lu.',	'livres-64766c67c7e2b.jpg',	'2023-05-30 23:35:07',	NULL),
(6,	1,	6,	'j\'adore les smatphones',	'c\'est plus fort que moi, un nouvel appareil tous les 6 mois max :p',	'phones-64766ce300481.jpg',	'2023-05-30 23:38:42',	NULL),
(7,	12,	12,	'pieces anciennes',	'mes plus rares pieces',	'pieces-64766e0993a6b.jpg',	'2023-05-30 23:43:37',	NULL),
(8,	13,	7,	'j\'adore sentir bon',	'un petit aperçu de ma collection de parfums',	'parfums-64766f0f95370.jpg',	'2023-05-30 23:47:59',	NULL),
(9,	12,	8,	'je collectionne les timbres',	'j\'en possède actuellement plus de 1500.',	'timbres-64766fcbe3c1a.jpg',	'2023-05-30 23:51:07',	NULL),
(10,	15,	9,	'vive les pin\'s',	'j\'adore les pin\'s même si c\'est passé de mode.',	'pins-6476704841d44.jpg',	'2023-05-30 23:53:12',	NULL),
(11,	16,	10,	'mes propres oeuvres',	'voici mes pièces les plus abouties.',	'tableau-4-647670ab6a768.jpg',	'2023-05-30 23:54:51',	NULL),
(12,	17,	11,	'sneakers land',	'non! aucun rapport avec la barre chocolatée.',	'sneakers-3-6476719a97e7d.jpg',	'2023-05-30 23:58:50',	NULL),
(13,	9,	13,	'montres',	'mes plus belles montres',	'montre-6476f9495be82.jpg',	'2023-05-31 09:37:45',	NULL);

INSERT INTO `album_like` (`id`, `album_id`, `user_id`) VALUES
(8,	2,	2),
(9,	1,	2),
(10,	12,	11),
(11,	8,	11),
(12,	11,	11),
(13,	7,	11),
(14,	6,	11),
(15,	11,	3),
(16,	12,	3),
(17,	7,	3),
(18,	8,	3),
(19,	11,	4),
(20,	12,	4),
(21,	10,	4),
(22,	4,	4),
(23,	11,	5),
(24,	12,	5),
(25,	11,	13);

INSERT INTO `category` (`id`, `name`) VALUES
(1,	'jeuxvideos'),
(2,	'sneakers'),
(3,	'livres'),
(4,	'pin\'s'),
(6,	'figurines'),
(8,	'tableau'),
(9,	'montres'),
(10,	'telephones'),
(11,	'parfums'),
(12,	'pièces'),
(13,	'timbres');

INSERT INTO `collectible` (`id`, `user_id_id`, `name`, `description`, `release_date`, `picture`, `poster`, `created_at`, `updated_at`) VALUES
(1,	2,	'Bowser',	'édition deluxe ultimate',	'2025-01-01 00:00:00',	'bowser-6476602f38781.jpg',	NULL,	'2023-05-30 18:59:52',	NULL),
(2,	2,	'bowser 2',	'j\'adore cette figurine',	'2025-01-01 00:00:00',	'bowser2-6476e325413b6.jpg',	NULL,	'2023-05-30 19:01:45',	NULL),
(3,	2,	'azezzae',	'azeazeazeazeazeaz',	'2025-01-01 00:00:00',	'marvel-6476397fe9a6b.jpg',	NULL,	'2023-05-30 19:59:13',	NULL),
(4,	2,	'bowser 3',	'encore et toujours le grand bowser',	'2025-01-01 00:00:00',	'bowser3-6476e34c0a4c6.jpg',	NULL,	'2023-05-30 20:30:25',	NULL),
(5,	4,	'vieux tel 1',	'les portables de l\'époque :p',	'2025-01-01 00:00:00',	'oldphones-3-64766b771c9e2.jpg',	NULL,	'2023-05-30 23:32:39',	NULL),
(6,	4,	'vieux tel 2',	'les portables de l\'époque :p bis',	'2025-01-01 00:00:00',	'oldphones-2-64766b900079b.jpg',	NULL,	'2023-05-30 23:33:03',	NULL),
(7,	6,	'ma petite collec 1',	'mes préférés en gros plan',	'2025-01-01 00:00:00',	'phones-2-64766d078d60a.jpg',	NULL,	'2023-05-30 23:39:19',	NULL),
(8,	6,	'ma petite collec 2',	'quelques exemplaires en vrac...',	'2025-01-01 00:00:00',	'phones-3-64766d479a7d2.jpg',	NULL,	'2023-05-30 23:40:23',	NULL),
(9,	12,	'pieces 1',	'en argent datant de bob 1er.',	'2025-01-01 00:00:00',	'pieces-2-64766e639ac31.jpg',	NULL,	'2023-05-30 23:45:07',	NULL),
(10,	12,	'pieces 2',	'toujours en argent cette fois datant de bob 5 fils.',	'2025-01-01 00:00:00',	'pieces-3-64766e8158c29.jpg',	NULL,	'2023-05-30 23:45:37',	NULL),
(11,	12,	'pieces 3',	'vue d\'ensemble',	'2025-01-01 00:00:00',	'pieces-4-64766ec32776d.jpg',	NULL,	'2023-05-30 23:46:43',	NULL),
(12,	7,	'parfums 1',	'mes préférés',	'2025-01-01 00:00:00',	'parfums-2-64766f418d76c.jpg',	NULL,	'2023-05-30 23:48:32',	NULL),
(13,	7,	'parfums 2',	'mes autres préférés',	'2025-01-01 00:00:00',	'parfums-3-64766f61de67c.jpg',	NULL,	'2023-05-30 23:49:21',	NULL),
(14,	10,	'peinture 1',	'je l\'ai intitulé mixture de couleurs.',	'2025-01-01 00:00:00',	'tableau-2-647670e3694de.jpg',	NULL,	'2023-05-30 23:55:47',	NULL),
(15,	10,	'peinture 2',	'je vous présente fricassée de pinceaux.',	'2025-01-01 00:00:00',	'tableau-3-6476710d0fe18.jpg',	NULL,	'2023-05-30 23:56:29',	NULL),
(16,	10,	'peinture 3',	'et voici ouverture vers l\'infini.',	'2025-01-01 00:00:00',	'tableau-4-647671292921e.jpg',	NULL,	'2023-05-30 23:56:57',	NULL),
(17,	11,	'sneakers 1',	'ma préférée.',	'2025-01-01 00:00:00',	'sneakers-2-647671bd9aaf1.jpg',	NULL,	'2023-05-30 23:59:25',	NULL),
(18,	11,	'sneakers 2',	'ma 2ème préférée avant la 1ère',	'2025-01-01 00:00:00',	'sneakers-647671df7d573.jpg',	NULL,	'2023-05-30 23:59:59',	NULL),
(19,	11,	'sneakers 3',	'mon ancienne préférée.',	'2025-01-01 00:00:00',	'sneakers-5-647671ff2baa9.jpg',	NULL,	'2023-05-31 00:00:31',	NULL),
(20,	11,	'sneakers 4',	'ma futur ex ancienne préférée.',	'2025-01-01 00:00:00',	'sneakers-4-6476721840363.jpg',	NULL,	'2023-05-31 00:00:56',	NULL),
(21,	3,	'donald',	'mon préféré',	'2025-01-01 00:00:00',	'disney3-647681f0386ed.jpg',	NULL,	'2023-05-31 01:08:32',	NULL),
(22,	3,	'la star',	'mickey the best',	'2025-01-01 00:00:00',	'disney2-6476820dddec7.jpg',	NULL,	'2023-05-31 01:09:01',	NULL),
(23,	3,	'princesse',	'la classe.',	'2025-01-01 00:00:00',	'disney-647682287c8f3.jpg',	NULL,	'2023-05-31 01:09:28',	NULL),
(24,	9,	'pins 1',	'mes pin\'s en vrac',	'2025-01-01 00:00:00',	'pins2-647682e0070cf.jpg',	NULL,	'2023-05-31 01:12:31',	NULL),
(25,	13,	'montre 1',	'azeaze',	'2025-01-01 00:00:00',	'montre-6476f96948cef.jpg',	NULL,	'2023-05-31 09:38:17',	NULL),
(26,	13,	'azeaze',	'azeaeaea',	'2025-01-01 00:00:00',	NULL,	NULL,	'2023-05-31 09:38:25',	NULL),
(27,	13,	'azaz',	'eaaeazea',	'2025-01-01 00:00:00',	'montres2-6476f97bb63c2.jpg',	NULL,	'2023-05-31 09:38:35',	NULL),
(28,	13,	'eaea',	'aeaeae',	'2025-01-01 00:00:00',	'montres3-6476f985b826b.jpg',	NULL,	'2023-05-31 09:38:45',	NULL),
(29,	13,	'eazae',	'aeaeaea',	'2025-01-01 00:00:00',	'montres5-6476f995a5722.jpg',	NULL,	'2023-05-31 09:39:01',	NULL);

INSERT INTO `collectible_album` (`collectible_id`, `album_id`) VALUES
(1,	1),
(2,	1),
(3,	2),
(4,	1),
(5,	4),
(6,	4),
(7,	6),
(8,	6),
(9,	7),
(10,	7),
(11,	7),
(12,	8),
(13,	8),
(14,	11),
(15,	11),
(16,	11),
(17,	12),
(18,	12),
(19,	12),
(20,	12),
(21,	3),
(22,	3),
(23,	3),
(24,	10),
(25,	13),
(26,	13),
(27,	13),
(28,	13),
(29,	13);

INSERT INTO `collectible_category` (`collectible_id`, `category_id`) VALUES
(1,	1),
(2,	1),
(2,	6),
(3,	1),
(4,	1),
(4,	6),
(5,	10),
(6,	10),
(7,	10),
(8,	10),
(9,	12),
(10,	12),
(11,	12),
(12,	11),
(13,	11),
(14,	8),
(15,	8),
(16,	8),
(17,	2),
(18,	2),
(19,	2),
(20,	2),
(21,	6),
(22,	6),
(23,	6),
(24,	4),
(25,	1),
(26,	6),
(27,	8),
(28,	9),
(29,	8);

INSERT INTO `doctrine_migration_versions` (`version`, `executed_at`, `execution_time`) VALUES
('DoctrineMigrations\\Version20230530161046',	'2023-05-30 18:11:00',	1035);

INSERT INTO `message` (`id`, `sender_id`, `recipient_id`, `title`, `content`, `created_at`, `is_read`, `updated_at`, `active`, `active2`) VALUES
(1,	13,	11,	'yo',	'aeazeazeazeaa',	'2023-05-31 09:42:21',	0,	NULL,	1,	1),
(2,	11,	13,	'zaeaze',	'azeazea',	'2023-05-31 09:42:45',	1,	NULL,	1,	1);


INSERT INTO `review` (`id`, `user_id`, `album_id`, `comment`, `rating`, `published_at`, `updated_at`) VALUES
(1,	2,	1,	'wouaaaaaaa incroyable cette collection',	5,	'2023-05-30 20:23:05',	NULL),
(2,	11,	11,	'ca va bien dans le style.',	5,	'2023-05-31 00:25:25',	NULL),
(3,	10,	12,	'jolie collection, pas trop dur de choisir la bonne paire le matin?',	5,	'2023-05-31 00:26:26',	NULL),
(4,	10,	8,	'sympas',	4,	'2023-05-31 00:27:00',	NULL),
(5,	10,	7,	'ca vaut cher ces trucs de nos jours?',	4,	'2023-05-31 00:27:30',	NULL),
(6,	10,	6,	'un vrai magasin lol :)',	5,	'2023-05-31 00:29:49',	NULL),
(8,	6,	12,	'rien qu\'à voir toutes ces baskets je suis essouflé...',	5,	'2023-05-31 00:52:41',	NULL),
(9,	13,	11,	'j\'adore ce que vous faites gg',	5,	'2023-05-31 09:41:17',	NULL);

INSERT INTO `review_like` (`id`, `review_id`, `user_id`) VALUES
(1,	1,	2),
(2,	8,	10),
(3,	9,	13),
(4,	2,	13);

INSERT INTO `universe` (`id`, `name`) VALUES
(1,	'jeux'),
(3,	'marvel'),
(8,	'sport'),
(9,	'horlogerie'),
(10,	'hightech'),
(11,	'culture'),
(12,	'numismatique'),
(13,	'bien-être'),
(14,	'philatélie'),
(15,	'deco'),
(16,	'art'),
(17,	'chaussures');

INSERT INTO `user` (`id`, `email`, `username`, `roles`, `password`, `picture`, `summary`) VALUES
(1,	'user@user.com',	'user',	'[]',	'$2y$13$DQkRd7ffj3Xf5I3bemFg4.fXEoCtz2DxZnCl0cUUX.ffcEp06xJAO',	'bowser-647629e5c76e3.jpg',	'j\'adore mario, surtout bowser il est trop fort.'),
(2,	'admin@admin.com',	'admin',	'[\"ROLE_ADMIN\"]',	'$2y$13$7JItWhoelbhiRBD5Tq3m/ehhYiyjwBHJZJRzUkQ/U2rSwNQexxtc6',	'Filmandclapboard-6476440803c86.jpg',	'bonjour je suis dieu, attention à vous :). '),
(3,	'claude@claude.com',	'claude',	'[]',	'$2y$13$BSHchUhesTot8yflPCAKtu0KeC2gSyfvmkgD.2pRTC6JVi3Z2S9ka',	'figurines-disney-647676c05cf31.jpg',	'vive disney!!! '),
(4,	'lucie@lucie.com',	'lucie',	'[]',	'$2y$13$i/xFIJf.xdeoSKO4DUrziehdDTdCjk3h2Rvlu.5nHCt45WhAeHSQW',	'profil-lucie-6476753f796eb.jpg',	'franchement!  on a jamais fait mieux que le 3210. '),
(5,	'roger@roger.com',	'roger',	'[]',	'$2y$13$dfA/kWLwVvjsC6IgnUH8le0UwV/JixzA0t1EXhmfteH.pivjzt242',	'livres-647675fc962ff.jpg',	'j\'ai pas lu le quart de ma biblio ma ca rend super bien nan? '),
(6,	'peter@peter.com',	'peter',	'[]',	'$2y$13$bnoJAV5oiWXC/odybBVZQeRV0Ck7flmqsxQSrcrmqjmtVMDPgRPNK',	'phones-6476763c5ab99.jpg',	'allooooooooooooooooooooooooooooo!?'),
(7,	'laure@laure.com',	'laure',	'[]',	'$2y$13$AKcngX/2p3g4KVnuuAUzRuFOxw6/mgr2g45oX4zcm4bhlgmUqsEGG',	'parfums-6476769cd3459.jpg',	'j\'ai appris à sentir bon avant d\'apprendre à respirer lol.'),
(8,	'helene@helene.com',	'helene',	'[]',	'$2y$13$obDTIA4MM1RFMw1q2uyXvOIZDjLyY4yY0u1Fv3KU12HtXRA.Olo4W',	'timbres-6476761c8ca04.jpg',	'je suis moi-même un peu timbrée :) '),
(9,	'monique@monique.com',	'monique',	'[]',	'$2y$13$TItVMwYHJdcXiBCDhdhOYugxPHiRtbs68eBqxCApwVhT7jnjsongm',	'pins-647676e7478f2.jpg',	NULL),
(10,	'harry@harry.com',	'harry',	'[]',	'$2y$13$U5Vk4kibQXjchbBjI20/q.sGQUMio8Rt.mvmbNGGc.0uO49XAoriO',	'harry-profil-6476757fbefe5.jpg',	'j\'aimerais faire de la peinture mon métier.'),
(11,	'alex@alex.com',	'alex',	'[]',	'$2y$13$PEdz31ZUWn3Q49SSsiAIkeqv.aSzkNNav2iwRpuxyls.qyZ5ilVm6',	'alex-profil-6476759a42b42.jpg',	'la course, c\'est ma grande passion...'),
(12,	'steeve@steeve.com',	'steeve',	'[]',	'$2y$13$AhZW8fFYQ8IYngnNXjWYduRG6ZZ7TUSX2nITMbNuItcBeiec5dkP6',	'pieces-647674d2415db.jpg',	'give me the money!!!'),
(13,	'micka@micka.com',	'micka',	'[]',	'$2y$13$FPUroxJsGMA5Xdi8aIiC9uDxoMvLSDP.3gQPDD7ulaGNIY3bFgbOW',	'montreprof-6476f91877aee.jpg',	'j\'adore les montres dans 20 30 ans yen aura plus.');

-- 2023-07-10 15:18:47
