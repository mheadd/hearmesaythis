-- 
-- Table structure for table `twitter_queue`
-- 

CREATE TABLE `twitter_queue` (
  `messageid` varchar(32) NOT NULL,
  `datetime` datetime NOT NULL,
  `status` int(1) NOT NULL default '0',
  `sender` varchar(75) NOT NULL,
  `state` varchar(2) NOT NULL,
  `sendtolist` varchar(120) NOT NULL,
  `sendtobioid` varchar(75) NOT NULL,
  `sendtoname` varchar(250) NOT NULL,
  PRIMARY KEY  (`messageid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

