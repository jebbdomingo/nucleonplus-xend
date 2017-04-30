--
-- Table structure for table `#__xend_shippingrates`
--

CREATE TABLE IF NOT EXISTS `#__xend_shippingrates` (
  `xend_shippingrate_id` int(11) NOT NULL AUTO_INCREMENT,
  `max_weight` int(11) NOT NULL COMMENT 'Grams',
  `destination` varchar(50) DEFAULT NULL,
  `label` varchar(50) DEFAULT NULL,
  `rate` decimal(10,2) NOT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_on` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  PRIMARY KEY (`xend_shippingrate_id`),
  KEY `name` (`destination`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

--
-- Dumping data for table `#__xend_shippingrates`
--

INSERT INTO `#__xend_shippingrates` (`xend_shippingrate_id`, `max_weight`, `destination`, `label`, `rate`, `modified_by`, `modified_on`, `created_by`, `created_on`) VALUES
(1, 1000, 'manila', 'Metro Manila', 69.00, NULL, NULL, NULL, NULL),
(2, 3000, 'manila', 'Metro Manila', 89.00, NULL, NULL, NULL, NULL),
(3, 1000, 'manila_kg', 'Metro Manila Additional KG', 30.00, NULL, NULL, NULL, NULL),
(4, 1000, 'provincial', 'Province', 109.00, NULL, NULL, NULL, NULL),
(5, 3000, 'provincial', 'Province', 179.00, NULL, NULL, NULL, NULL),
(6, 1000, 'provincial_kg', 'Province Additional KG', 80.00, NULL, NULL, NULL, NULL);
