-- phpMyAdmin SQL Dump
-- version 3.5.8
-- http://www.phpmyadmin.net
--
-- Servidor: localhost
-- Tiempo de generación: 04-07-2013 a las 01:21:32
-- Versión del servidor: 5.1.70-cll
-- Versión de PHP: 5.3.17

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de datos: `spickly_spickly`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `chat`
--

CREATE TABLE IF NOT EXISTS `chat` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `member_id_send` int(11) unsigned NOT NULL,
  `member_id_receive` int(11) unsigned NOT NULL,
  `message` text NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `fk_chat_members1_idx` (`member_id_send`),
  KEY `fk_chat_members2_idx` (`member_id_receive`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `comment_events`
--

CREATE TABLE IF NOT EXISTS `comment_events` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `member_id_send` int(11) unsigned NOT NULL,
  `events_id` int(11) NOT NULL,
  `text` varchar(300) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_comment_event_members1_idx` (`member_id_send`),
  KEY `fk_comment_event_events1_idx` (`events_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `comment_pages`
--

CREATE TABLE IF NOT EXISTS `comment_pages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `member_id_send` int(11) unsigned NOT NULL,
  `pages_id` int(11) NOT NULL,
  `text` varchar(300) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_comment_pages_members1_idx` (`member_id_send`),
  KEY `fk_comment_pages_pages1_idx` (`pages_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='members_member_id' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `comment_photos`
--

CREATE TABLE IF NOT EXISTS `comment_photos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `member_id_send` int(11) unsigned NOT NULL,
  `photos_id` int(11) NOT NULL,
  `text` varchar(500) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `parent` (`id`),
  KEY `fk_comment_photos_photos1_idx` (`photos_id`),
  KEY `fk_comment_photos_members1_idx` (`member_id_send`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `comment_spacers`
--

CREATE TABLE IF NOT EXISTS `comment_spacers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `member_id_send` int(11) unsigned NOT NULL,
  `spacers_id` int(11) NOT NULL,
  `text` varchar(500) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_comment_spacers_spacer1_idx` (`spacers_id`,`member_id_send`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `comment_users`
--

CREATE TABLE IF NOT EXISTS `comment_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `member_id_send` int(11) unsigned NOT NULL,
  `member_id_receive` int(11) unsigned NOT NULL,
  `parent_comment` int(11) NOT NULL,
  `text` varchar(500) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `parent` (`id`),
  KEY `fk_comment_users_comment_users1_idx` (`parent_comment`),
  KEY `fk_comment_users_members1_idx` (`member_id_receive`),
  KEY `fk_comment_users_members2_idx` (`member_id_send`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `events`
--

CREATE TABLE IF NOT EXISTS `events` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `member_id_send` int(11) unsigned NOT NULL,
  `topic` varchar(50) NOT NULL,
  `text` varchar(500) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `place` varchar(255) NOT NULL,
  `contact` varchar(25) NOT NULL,
  `photos_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_eventos_members1_idx` (`member_id_send`),
  KEY `fk_events_photos1_idx` (`photos_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `friends`
--

CREATE TABLE IF NOT EXISTS `friends` (
  `member_id_send` int(11) unsigned NOT NULL,
  `member_id_receive` int(11) unsigned NOT NULL,
  `alive` tinyint(1) NOT NULL DEFAULT '0',
  `ftype` tinyint(1) NOT NULL DEFAULT '0',
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`member_id_send`,`member_id_receive`),
  KEY `fk_friends_members1_idx` (`member_id_send`),
  KEY `fk_friends_members2_idx` (`member_id_receive`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `like_photos`
--

CREATE TABLE IF NOT EXISTS `like_photos` (
  `member_id_send` int(11) unsigned NOT NULL,
  `photos_id` int(11) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`member_id_send`,`photos_id`),
  KEY `fk_like_photos_members1_idx` (`member_id_send`),
  KEY `fk_like_photos_photos1_idx` (`photos_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `members`
--

CREATE TABLE IF NOT EXISTS `members` (
  `member_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `firstname` varchar(100) NOT NULL,
  `lastname` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `passwd` varchar(32) NOT NULL,
  `birthdate` date NOT NULL,
  `city` varchar(50) NOT NULL,
  `sex` tinyint(1) NOT NULL,
  `status` tinyint(1) NOT NULL,
  `prf_img` int(11) NOT NULL,
  `last_visit` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `num_visits` int(11) NOT NULL,
  `iam` tinyint(1) NOT NULL,
  `ilikeit` varchar(300) NOT NULL,
  `bio` varchar(300) NOT NULL,
  PRIMARY KEY (`member_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `members_has_events`
--

CREATE TABLE IF NOT EXISTS `members_has_events` (
  `member_id_send` int(11) unsigned NOT NULL,
  `member_id_receive` int(11) unsigned NOT NULL,
  `events_id` int(11) NOT NULL,
  `alive` int(11) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`member_id_send`,`member_id_receive`,`events_id`),
  KEY `fk_members_has_evento_events1_idx` (`events_id`),
  KEY `fk_members_has_evento_members1_idx` (`member_id_send`),
  KEY `fk_members_has_evento_members2_idx` (`member_id_receive`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `members_has_pages`
--

CREATE TABLE IF NOT EXISTS `members_has_pages` (
  `member_id_send` int(11) unsigned NOT NULL,
  `member_id_receive` int(11) unsigned NOT NULL,
  `pages_id` int(11) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `alive` int(11) NOT NULL,
  PRIMARY KEY (`member_id_receive`,`pages_id`),
  KEY `fk_members_has_pages_members1_idx` (`member_id_send`),
  KEY `fk_members_has_pages_members2_idx` (`member_id_receive`),
  KEY `fk_members_has_pages_pages1_idx` (`pages_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mps`
--

CREATE TABLE IF NOT EXISTS `mps` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `member_id_send` int(11) unsigned NOT NULL,
  `member_id_receive` int(11) unsigned NOT NULL,
  `topic` varchar(100) NOT NULL,
  `message` varchar(5000) NOT NULL,
  `readed` int(11) unsigned NOT NULL DEFAULT '0',
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_mp_members1_idx` (`member_id_send`),
  KEY `fk_mp_members2_idx` (`member_id_receive`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Account System' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `notes`
--

CREATE TABLE IF NOT EXISTS `notes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `member_id_send` int(11) unsigned NOT NULL,
  `text` varchar(255) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_notas_members1_idx` (`member_id_send`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pages`
--

CREATE TABLE IF NOT EXISTS `pages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `member_id_send` int(11) unsigned NOT NULL,
  `topic` varchar(100) NOT NULL,
  `text` varchar(1000) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `category` int(11) NOT NULL,
  `photos_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_pages_members1_idx` (`member_id_send`),
  KEY `fk_pages_photos1_idx` (`photos_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `photos`
--

CREATE TABLE IF NOT EXISTS `photos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `member_id_send` int(11) unsigned NOT NULL,
  `image_url` varchar(100) NOT NULL,
  `name` varchar(100) NOT NULL DEFAULT 'Sin Titulo',
  `desc` varchar(500) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_photos_members1_idx` (`member_id_send`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `polls`
--

CREATE TABLE IF NOT EXISTS `polls` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `event_id` int(11) NOT NULL,
  `topic` varchar(50) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_encuestas_eventos1_idx` (`event_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `poll_options`
--

CREATE TABLE IF NOT EXISTS `poll_options` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `poll_id` int(11) NOT NULL,
  `text` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_opciones_encuestas1_idx` (`poll_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `poll_votes`
--

CREATE TABLE IF NOT EXISTS `poll_votes` (
  `poll_id` int(11) NOT NULL,
  `option_id` int(11) NOT NULL,
  `member_id_send` int(11) unsigned NOT NULL,
  PRIMARY KEY (`poll_id`,`option_id`,`member_id_send`),
  KEY `fk_result_encu_encuestas1_idx` (`poll_id`),
  KEY `fk_result_encu_opciones1_idx` (`option_id`),
  KEY `fk_result_encu_members1_idx` (`member_id_send`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `spacers`
--

CREATE TABLE IF NOT EXISTS `spacers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `member_id_send` int(11) unsigned NOT NULL,
  `text` varchar(1000) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_spacer_members1_idx` (`member_id_send`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tags`
--

CREATE TABLE IF NOT EXISTS `tags` (
  `member_id_send` int(11) unsigned NOT NULL,
  `member_id_receive` int(11) unsigned NOT NULL,
  `photos_id` int(11) NOT NULL,
  `left` int(11) NOT NULL,
  `top` int(11) NOT NULL,
  `width` int(11) NOT NULL,
  `height` int(11) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`member_id_send`,`member_id_receive`,`photos_id`),
  KEY `fk_etiquetas_members_idx` (`member_id_receive`),
  KEY `fk_etiquetas_members1_idx` (`member_id_send`),
  KEY `fk_etiquetas_photos1_idx` (`photos_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
