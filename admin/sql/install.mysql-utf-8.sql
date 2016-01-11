CREATE TABLE IF NOT EXISTS `#__jeproshop_category` (
  `category_id` int(10) unsigned NOT NULL auto_increment,
  `parent_id` int(10) unsigned NOT NULL,
  `default_shop_id` int(10) unsigned NOT NULL default 1,
  `depth_level` tinyint(3) unsigned NOT NULL default '0',
  `n_left` int(10) unsigned NOT NULL default '0',
  `n_right` int(10) unsigned NOT NULL default '0',
  `published` tinyint(1) unsigned NOT NULL default '0',
  `date_add` datetime NOT NULL,
  `date_upd` datetime NOT NULL,
  `position` int(10) unsigned NOT NULL default '0',
  `is_root_category` tinyint(1) NOT NULL default '0',
  PRIMARY KEY (`category_id`),
  KEY `category_parent` (`parent_id`),
  KEY `n_left_right` (`n_left`, `n_right`),
  KEY `n_left_right_published` (`n_left`, `n_right`, `published`),
  KEY `depth_level` (`depth_level`),
  KEY `n_right` (`n_right`),
  KEY `n_left` (`n_left`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeproshop_category_lang` (
  `category_id` int(10) unsigned NOT NULL,
  `shop_id` INT( 11 ) UNSIGNED NOT NULL DEFAULT '1',
  `lang_id` int(10) unsigned NOT NULL,
  `name` varchar(128) NOT NULL,
  `description` text,
  `link_rewrite` varchar(128) NOT NULL,
  `meta_title` varchar(128) default NULL,
  `meta_keywords` varchar(255) default NULL,
  `meta_description` varchar(255) default NULL,
  PRIMARY KEY (`category_id`,`shop_id`, `lang_id`),
  KEY `category_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeproshop_shop_group` (
  `shop_group_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(64) CHARACTER SET utf8 NOT NULL,
  `share_customer` TINYINT(1) NOT NULL,
  `share_order` TINYINT(1) NOT NULL,
  `share_stock` TINYINT(1) NOT NULL,
  `published` tinyint(1) NOT NULL DEFAULT '1',
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`shop_group_id`)) ENGINE=InnoDB  DEFAULT CHARACTER SET=utf8;

  CREATE TABLE IF NOT EXISTS `#__jeproshop_theme` (
  `theme_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  `directory` varchar(64) NOT NULL,
  `responsive` tinyint(1) NOT NULL DEFAULT '0',
  `default_left_column` tinyint(1) NOT NULL DEFAULT '0',
  `default_right_column` tinyint(1) NOT NULL DEFAULT '0',
  `product_per_page` int(10) unsigned NOT NULL,
  PRIMARY KEY (`theme_id`)
) ENGINE=InnoDB  DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeproshop_shop` (
  `shop_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `shop_group_id` int(11) unsigned NOT NULL,
  `shop_name` varchar(64) CHARACTER SET utf8 NOT NULL,
  `category_id` INT(11) UNSIGNED NOT NULL DEFAULT '1',
  `theme_id` INT(1) UNSIGNED NOT NULL,
  `published` tinyint(1) NOT NULL DEFAULT '1',
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`shop_id`),
  KEY `shop_group_id` (`shop_group_id`),
  KEY `category_id` (`category_id`),
  KEY `theme_id` (`theme_id`)
) ENGINE=InnoDB  DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeproshop_language_shop` (
	`lang_id` INT( 11 ) UNSIGNED NOT NULL,
	`shop_id` INT( 11 ) UNSIGNED NOT NULL,
  	PRIMARY KEY (`lang_id`, `shop_id`),
	KEY `shop_id` (`shop_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeproshop_category_shop` (
  `category_id` int(11) NOT NULL,
  `shop_id` int(11) NOT NULL,
  `position` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY (`category_id`, `shop_id`)
) ENGINE=InnoDB  DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeproshop_shop_url` (
  `shop_url_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `shop_id` int(11) unsigned NOT NULL,
  `domain` varchar(150) NOT NULL,
  `ssl_domain` varchar(150) NOT NULL,
  `physical_uri` varchar(64) NOT NULL,
  `virtual_uri` varchar(64) NOT NULL,
  `main` TINYINT(1) NOT NULL,
  `published` TINYINT(1) NOT NULL,
  PRIMARY KEY (`shop_url_id`),
  KEY `shop_id` (`shop_id`),
  UNIQUE KEY `full_shop_url` (`domain`, `physical_uri`, `virtual_uri`),
  UNIQUE KEY `full_shop_url_ssl` (`ssl_domain`, `physical_uri`, `virtual_uri`)
) ENGINE=InnoDB  DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeproshop_country` (
  `country_id` int(10) unsigned NOT NULL auto_increment,
  `zone_id` int(10) unsigned NOT NULL,
  `currency_id` int(10) unsigned NOT NULL default '0',
  `iso_code` varchar(3) NOT NULL,
  `call_prefix` int(10) NOT NULL default '0',
  `published` tinyint(1) unsigned NOT NULL default '0',
  `contains_states` tinyint(1) NOT NULL default '0',
  `need_identification_number` tinyint(1) NOT NULL default '0',
  `need_zip_code` tinyint(1) NOT NULL default '1',
  `zip_code_format` varchar(12) NOT NULL default '',
  `display_tax_label` BOOLEAN NOT NULL,
  PRIMARY KEY (`country_id`),
  KEY `country_iso_code` (`iso_code`),
  KEY `country_zone` (`zone_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeproshop_country_lang` (
  `country_id` int(10) unsigned NOT NULL,
  `lang_id` int(10) unsigned NOT NULL,
  `name` varchar(64) NOT NULL,
  PRIMARY KEY (`country_id`,`lang_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeproshop_country_shop` (
 `country_id` INT( 11 ) UNSIGNED NOT NULL,
 `shop_id` INT( 11 ) UNSIGNED NOT NULL ,
  PRIMARY KEY (`country_id`, `shop_id`),
  KEY `shop_id` (`shop_id`)
) ENGINE=InnoDB  DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeproshop_zone` (
  `zone_id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(64) NOT NULL,
  `allow_delivery` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY (`zone_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeproshop_zone_shop` (
  `zone_id` INT( 11 ) UNSIGNED NOT NULL ,
  `shop_id` INT( 11 ) UNSIGNED NOT NULL ,
  PRIMARY KEY (`zone_id`, `shop_id`),
  KEY `shop_id` (`shop_id`)
) ENGINE=InnoDB  DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeproshop_currency` (
  `currency_id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(32) NOT NULL,
  `iso_code` varchar(3) NOT NULL default '0',
  `iso_code_num` varchar(3) NOT NULL default '0',
  `sign` varchar(8) NOT NULL,
  `blank` tinyint(1) unsigned NOT NULL default '0',
  `format` tinyint(1) unsigned NOT NULL default '0',
  `decimals` tinyint(1) unsigned NOT NULL default '1',
  `conversion_rate` decimal(13,6) NOT NULL,
  `deleted` tinyint(1) unsigned NOT NULL default '0',
  `published` tinyint(1) unsigned NOT NULL default '1',
  PRIMARY KEY (`currency_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeproshop_currency_shop` (
  `currency_id` INT( 11 ) UNSIGNED NOT NULL,
  `shop_id` INT( 11 ) UNSIGNED NOT NULL,
  `conversion_rate` decimal(13,6) NOT NULL,
  PRIMARY KEY (`currency_id`, `shop_id`),
  KEY `shop_id` (`shop_id`)
) ENGINE=InnoDB  DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeproshop_state` (
  `state_id` int(10) unsigned NOT NULL auto_increment,
  `country_id` int(11) unsigned NOT NULL,
  `zone_id` int(11) unsigned NOT NULL,
  `name` varchar(64) NOT NULL,
  `iso_code` varchar(7) NOT NULL,
  `tax_behavior` smallint(1) NOT NULL default '0',
  `published` tinyint(1) NOT NULL default '0',
  PRIMARY KEY (`state_id`),
  KEY `country_id` (`country_id`),
  KEY `name` (`name`),
  KEY `zone_id` (`zone_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeproshop_product` (
  `product_id` int(10) unsigned NOT NULL auto_increment,
  `supplier_id` int(10) unsigned default NULL,
  `developer_id` int(10) unsigned default NULL,
  `manufacturer_id` int(10) unsigned default NULL,
  `default_category_id` int(10) unsigned default NULL,
  `default_shop_id` int(10) unsigned NOT NULL default 1,
  `tax_rules_group_id` INT(11) UNSIGNED NOT NULL,
  `on_sale` tinyint(1) unsigned NOT NULL default '0',
  `online_only` tinyint(1) unsigned NOT NULL default '0',
  `ean13` varchar(13) default NULL,
  `upc` varchar(12) default NULL,
  `ecotax` decimal(17,6) NOT NULL default '0.00',
  `quantity` int(10) NOT NULL default '0',
  `minimal_quantity` int(10) unsigned NOT NULL default '1',
  `price` decimal(20,6) NOT NULL default '0.000000',
  `wholesale_price` decimal(20,6) NOT NULL default '0.000000',
  `unity` varchar(255) default NULL,
  `unit_price_ratio` decimal(20,6) NOT NULL default '0.000000',
  `additional_shipping_cost` decimal(20,2) NOT NULL default '0.00',
  `reference` varchar(32) default NULL,
  `supplier_reference` varchar(32) default NULL,
  `location` varchar(64) default NULL,
  `width` DECIMAL(20, 6) NOT NULL default '0',
  `height` DECIMAL(20, 6) NOT NULL default '0',
  `depth` DECIMAL(20, 6) NOT NULL default '0',
  `weight` DECIMAL(20, 6) NOT NULL default '0',
  `out_of_stock` int(10) unsigned NOT NULL default '2',
  `quantity_discount` tinyint(1) default '0',
  `customizable` tinyint(2) NOT NULL default '0',
  `uploadable_files` tinyint(4) NOT NULL default '0',
  `text_fields` tinyint(4) NOT NULL default '0',
  `published` tinyint(1) unsigned NOT NULL default '0',
  `redirect_type` ENUM('', '404', '301', '302') NOT NULL DEFAULT '',
  `product_redirected_id` int(10) unsigned NOT NULL default '0',
  `available_for_order` tinyint(1) NOT NULL default '1',
  `available_date` date NOT NULL,
  `condition` ENUM('new', 'used', 'refurbished') NOT NULL DEFAULT 'new',
  `show_price` tinyint(1) NOT NULL default '1',
  `indexed` tinyint(1) NOT NULL default '0',
  `visibility` ENUM('both', 'catalog', 'search', 'none') NOT NULL default 'both',
  `cache_is_pack` tinyint(1) NOT NULL default '0',
  `cache_has_attachments` tinyint(1) NOT NULL default '0',
  `is_virtual` tinyint(1) NOT NULL default '0',
  `cache_default_attribute` int(10) unsigned default NULL,
  `date_add` datetime NOT NULL,
  `date_upd` datetime NOT NULL,
  `advanced_stock_management` tinyint(1) default '0' NOT NULL,
  PRIMARY KEY (`product_id`),
  KEY `product_supplier` (`supplier_id`),
  KEY `product_developer` (`developer_id`),
  KEY `product_manufacturer` (`manufacturer_id`),
  KEY `default_category_id` (`default_category_id`),
  KEY `indexed` (`indexed`),
  KEY `date_add` (`date_add`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeproshop_product_lang` (
  `product_id` int(10) unsigned NOT NULL,
  `shop_id` INT( 11 ) UNSIGNED NOT NULL DEFAULT '1',
  `lang_id` int(10) unsigned NOT NULL,
  `description` text,
  `short_description` text,
  `link_rewrite` varchar(128) NOT NULL,
  `meta_description` varchar(255) default NULL,
  `meta_keywords` varchar(255) default NULL,
  `meta_title` varchar(128) default NULL,
  `name` varchar(128) NOT NULL,
  `available_now` varchar(255) default NULL,
  `available_later` varchar(255) default NULL,
  PRIMARY KEY (`product_id`, `shop_id` , `lang_id`),
  KEY `lang_id` (`lang_id`),
  KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeproshop_product_shop` (
  `product_id` int(10) unsigned NOT NULL,
  `shop_id` int(10) unsigned NOT NULL,
  `default_category_id` int(10) unsigned DEFAULT NULL,
  `tax_rules_group_id` INT(11) UNSIGNED NOT NULL,
  `on_sale` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `online_only` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `ecotax` decimal(17,6) NOT NULL DEFAULT '0.000000',
  `minimal_quantity` int(10) unsigned NOT NULL DEFAULT '1',
  `price` decimal(20,6) NOT NULL DEFAULT '0.000000',
  `wholesale_price` decimal(20,6) NOT NULL DEFAULT '0.000000',
  `unity` varchar(255) DEFAULT NULL,
  `unit_price_ratio` decimal(20,6) NOT NULL DEFAULT '0.000000',
  `additional_shipping_cost` decimal(20,2) NOT NULL DEFAULT '0.00',
  `customizable` tinyint(2) NOT NULL DEFAULT '0',
  `uploadable_files` tinyint(4) NOT NULL default '0',
  `text_fields` tinyint(4) NOT NULL DEFAULT '0',
  `published` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `redirect_type` ENUM('', '404', '301', '302') NOT NULL DEFAULT '',
  `product_redirected_id` int(10) unsigned NOT NULL default '0',
  `available_for_order` tinyint(1) NOT NULL DEFAULT '1',
  `available_date` date NOT NULL,
  `condition` enum('new','used','refurbished') NOT NULL DEFAULT 'new',
  `show_price` tinyint(1) NOT NULL DEFAULT '1',
  `indexed` tinyint(1) NOT NULL DEFAULT '0',
  `visibility` enum('both','catalog','search','none') NOT NULL DEFAULT 'both',
  `cache_default_attribute` int(10) unsigned DEFAULT NULL,
  `advanced_stock_management` tinyint(1) default '0' NOT NULL,
  `date_add` datetime NOT NULL,
  `date_upd` datetime NOT NULL,
  PRIMARY KEY (`product_id`, `shop_id`),
  KEY `default_category_id` (`default_category_id`),
  KEY `date_add` (`date_add` , `published` , `visibility`)
) ENGINE=InnoDB  DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeproshop_product_download` (
  `product_download_id` int(10) unsigned NOT NULL auto_increment,
  `product_id` int(10) unsigned NOT NULL,
  `display_filename` tinyint(1) DEFAULT  1,
  `filename` VARCHAR(255) DEFAULT NULL,
  `date_add` DATETIME NOT NULL,
  `date_expiration` DATETIME NOT NULL,
  `nb_days_accessible` int(10) unsigned NOT NULL,
  `nb_downloadable` int(10) unsigned NOT NULL DEFAULT 1,
  `published` tinyint(1) unsigned DEFAULT  1,
  `ìs_sharable` tinyint(1) unsigned DEFAULT 1,
  PRIMARY KEY (`product_download_id`),
  KEY `product_published` (`product_id`, `published`),
  UNIQUE KEY `product_id` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeproshop_image` (
  `image_id` int(10) unsigned NOT NULL auto_increment,
  `product_id` int(10) unsigned NOT NULL,
  `position` smallint(2) unsigned NOT NULL default '0',
  `cover` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY (`image_id`),
  KEY `image_product` (`product_id`),
  KEY `product_cover_id` (`product_id`,`cover`),
  UNIQUE KEY `product_image_idx` (`image_id`, `product_id`, `cover`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeproshop_image_lang` (
  `image_id` int(10) unsigned NOT NULL,
  `lang_id` int(10) unsigned NOT NULL,
  `legend` varchar(128) default NULL,
  PRIMARY KEY (`image_id`,`lang_id`),
  KEY `image_id` (`image_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeproshop_image_type` (
  `image_type_id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(64) NOT NULL,
  `width` int(10) unsigned NOT NULL,
  `height` int(10) unsigned NOT NULL,
  `products` tinyint(1) NOT NULL default '1',
  `categories` tinyint(1) NOT NULL default '1',
  `manufacturers` tinyint(1) NOT NULL default '1',
  `suppliers` tinyint(1) NOT NULL default '1',
  `developers` tinyint(1) NOT NULL default '1',
  `scenes` tinyint(1) NOT NULL default '1',
  `stores` tinyint(1) NOT NULL default '1',
  PRIMARY KEY (`image_type_id`),
  KEY `image_type_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeproshop_stock_available` (
  `stock_available_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `product_id` INT(11) UNSIGNED NOT NULL,
  `product_attribute_id` INT(11) UNSIGNED NOT NULL,
  `shop_id` INT(11) UNSIGNED NOT NULL,
  `shop_group_id` INT(11) UNSIGNED NOT NULL,
  `quantity` INT(10) NOT NULL DEFAULT '0',
  `depends_on_stock` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
  `out_of_stock` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY (`stock_available_id`),
  KEY `shop_id` (`shop_id`),
  KEY `shop_group_id` (`shop_group_id`),
  KEY `product_id` (`product_id`),
  KEY `product_attribute_id` (`product_attribute_id`),
  UNIQUE `product_sql_stock` (`product_id` , `product_attribute_id` , `shop_id`, `shop_group_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeproshop_image_shop` (
    `image_id` INT( 11 ) UNSIGNED NOT NULL,
    `shop_id` INT( 11 ) UNSIGNED NOT NULL,
    `cover` tinyint(1) NOT NULL,
    KEY (`image_id`, `shop_id`, `cover`),
    KEY `shop_id` (`shop_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeproshop_product_attribute` (
  `product_attribute_id` int(10) unsigned NOT NULL auto_increment,
  `product_id` int(10) unsigned NOT NULL,
  `reference` varchar(32) default NULL,
  `supplier_reference` varchar(32) default NULL,
  `location` varchar(64) default NULL,
  `ean13` varchar(13) default NULL,
  `upc` varchar(12) default NULL,
  `wholesale_price` decimal(20,6) NOT NULL default '0.000000',
  `price` decimal(20,6) NOT NULL default '0.000000',
  `ecotax` decimal(17,6) NOT NULL default '0.00',
  `quantity` int(10) NOT NULL default '0',
  `weight` DECIMAL(20,6) NOT NULL default '0',
  `unit_price_impact` decimal(17,2) NOT NULL default '0.00',
  `default_on` tinyint(1) unsigned NOT NULL default '0',
  `minimal_quantity` int(10) unsigned NOT NULL DEFAULT '1',
  `available_date` date NOT NULL,
  PRIMARY KEY (`product_attribute_id`),
  KEY `product_attribute_product` (`product_id`),
  KEY `reference` (`reference`),
  KEY `supplier_reference` (`supplier_reference`),
  KEY `default_product` (`product_id`,`default_on`),
  KEY `product_id_product_attribute_id` (`product_attribute_id` , `product_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeproshop_product_attribute_combination` (
  `attribute_id` int(10) unsigned NOT NULL,
  `product_attribute_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`attribute_id`,`product_attribute_id`),
  KEY `product_attribute_id` (`product_attribute_id`)
)  ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE  IF NOT EXISTS `#__jeproshop_customization_field` (
  `customization_field_id` int(10) unsigned NOT NULL auto_increment,
  `product_id` int(10) unsigned NOT NULL,
  `type` tinyint(1) NOT NULL,
  `required` tinyint(1) NOT NULL,
  PRIMARY KEY (`customization_field_id`),
  KEY `product_id` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeproshop_customization_field_lang` (
  `customization_field_id` int(10) unsigned NOT NULL,
  `lang_id` int(10) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`customization_field_id`,`lang_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeproshop_attribute` (
  `attribute_id` int(10) unsigned NOT NULL auto_increment,
  `attribute_group_id` int(10) unsigned NOT NULL,
  `color` varchar(32) default NULL,
  `position` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY (`attribute_id`),
  KEY `attribute_group` (`attribute_group_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeproshop_attribute_lang` (
  `attribute_id` int(10) unsigned NOT NULL,
  `lang_id` int(10) unsigned NOT NULL,
  `name` varchar(128) NOT NULL,
  PRIMARY KEY (`attribute_id`,`lang_id`),
  KEY `lang_id` (`lang_id`,`name`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeproshop_attribute_shop` (
`attribute_id` INT(11) UNSIGNED NOT NULL,
`shop_id` INT(11) UNSIGNED NOT NULL,
PRIMARY KEY (`attribute_id`, `shop_id`),
KEY `shop_id` (`shop_id`)
) ENGINE=InnoDB  DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeproshop_attribute_group` (
  `attribute_group_id` int(10) unsigned NOT NULL auto_increment,
  `is_color_group` tinyint(1) NOT NULL default '0',
  `group_type` ENUM('select', 'radio', 'color') NOT NULL DEFAULT  'select',
  `position` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY (`attribute_group_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeproshop_attribute_group_lang` (
  `attribute_group_id` int(10) unsigned NOT NULL,
  `lang_id` int(10) unsigned NOT NULL,
  `name` varchar(128) NOT NULL,
  `public_name` varchar(64) NOT NULL,
  PRIMARY KEY (`attribute_group_id`,`lang_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeproshop_product_attribute_shop` (
  `product_attribute_id` int(10) unsigned NOT NULL,
  `shop_id` int(10) unsigned NOT NULL,
  `wholesale_price` decimal(20,6) NOT NULL default '0.000000',
  `price` decimal(20,6) NOT NULL default '0.000000',
  `ecotax` decimal(17,6) NOT NULL default '0.00',
  `weight` DECIMAL(20,6) NOT NULL default '0',
  `unit_price_impact` decimal(17,2) NOT NULL default '0.00',
  `default_on` tinyint(1) unsigned NOT NULL default '0',
  `minimal_quantity` int(10) unsigned NOT NULL DEFAULT '1',
  `available_date` date NOT NULL,
  PRIMARY KEY (`product_attribute_id`, `shop_id`)
)  ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeproshop_required_field` (
  `required_field_id` int(11) NOT NULL AUTO_INCREMENT,
  `object_name` varchar(32) NOT NULL,
  `field_name` varchar(32) NOT NULL,
  PRIMARY KEY (`required_field_id`),
  KEY `object_name` (`object_name`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeproshop_group_reduction` (
  `group_reduction_id` MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `group_id` INT(10) UNSIGNED NOT NULL,
  `category_id` INT(10) UNSIGNED NOT NULL,
  `reduction` DECIMAL(4, 3) NOT NULL,
  PRIMARY KEY (`group_reduction_id`),
  UNIQUE KEY(`group_id`, `category_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeproshop_group` (
  `group_id` int(10) unsigned NOT NULL auto_increment,
  `reduction` decimal(17,2) NOT NULL default '0.00',
  `price_display_method` TINYINT NOT NULL DEFAULT 0,
  `show_prices` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `date_add` datetime NOT NULL,
  `date_upd` datetime NOT NULL,
  PRIMARY KEY (`group_id`)
)ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeproshop_group_lang` (
  `group_id` int(10) unsigned NOT NULL,
  `lang_id` int(10) unsigned NOT NULL,
  `name` varchar(32) NOT NULL,
  PRIMARY KEY (`group_id`,`lang_id`)
)ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeproshop_group_shop` (
  `group_id` INT( 11 ) UNSIGNED NOT NULL,
  `shop_id` INT( 11 ) UNSIGNED NOT NULL,
    PRIMARY KEY (`group_id`, `shop_id`),
    KEY `shop_id` (`shop_id`)
)ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeproshop_tax_rules_group` (
    `tax_rules_group_id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
    `name` VARCHAR( 50 ) NOT NULL ,
    `published` INT NOT NULL
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeproshop_tax_rules_group_shop` (
    `tax_rules_group_id` INT( 11 ) UNSIGNED NOT NULL,
    `shop_id` INT( 11 ) UNSIGNED NOT NULL,
    PRIMARY KEY (`tax_rules_group_id`, `shop_id`),
    KEY `shop_id` (`shop_id`)
) ENGINE=InnoDB  DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeproshop_tax_rule` (
  `tax_rule_id` int(11) NOT NULL AUTO_INCREMENT,
  `tax_rules_group_id` int(11) NOT NULL,
  `country_id` int(11) NOT NULL,
  `state_id` int(11) NOT NULL,
  `zipcode_from` VARCHAR(12) NOT NULL,
  `zipcode_to` VARCHAR(12) NOT NULL,
  `tax_id` int(11) NOT NULL,
  `behavior` int(11) NOT NULL,
  `description` VARCHAR( 100 ) NOT NULL,
  PRIMARY KEY (`tax_rule_id`),
  KEY `tax_rules_group_id` (`tax_rules_group_id`),
  KEY `tax_id` (`tax_id`),
  KEY `category_get_products` ( `tax_rules_group_id` , `country_id` , `state_id` , `zipcode_from` )
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeproshop_tax` (
  `tax_id` int(10) unsigned NOT NULL auto_increment,
  `rate` DECIMAL(10, 3) NOT NULL,
  `published` tinyint(1) unsigned NOT NULL default '1',
  `deleted` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY (`tax_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeproshop_tax_lang` (
  `tax_id` int(10) unsigned NOT NULL,
  `lang_id` int(10) unsigned NOT NULL,
  `name` varchar(32) NOT NULL,
  PRIMARY KEY (`tax_id`,`lang_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeproshop_specific_price` (
    `specific_price_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `specific_price_rule_id` INT(11) UNSIGNED NOT NULL,
    `cart_id` INT(11) UNSIGNED NOT NULL,
    `product_id` INT UNSIGNED NOT NULL,
    `shop_id` INT(11) UNSIGNED NOT NULL DEFAULT '1',
    `shop_group_id` INT(11) UNSIGNED NOT NULL,
    `currency_id` INT UNSIGNED NOT NULL,
    `country_id` INT UNSIGNED NOT NULL,
    `group_id` INT UNSIGNED NOT NULL,
    `customer_id` INT UNSIGNED NOT NULL,
    `product_attribute_id` INT UNSIGNED NOT NULL,
    `price` DECIMAL(20, 6) NOT NULL,
    `from_quantity` mediumint(8) UNSIGNED NOT NULL,
    `reduction` DECIMAL(20, 6) NOT NULL,
    `reduction_type` ENUM('amount', 'percentage') NOT NULL,
    `from` DATETIME NOT NULL,
    `to` DATETIME NOT NULL,
    PRIMARY KEY (`specific_price_id`),
    KEY (`product_id`, `shop_id`, `currency_id`, `country_id`, `group_id`, `customer_id`, `from_quantity`, `from`, `to`),
    KEY `from_quantity` (`from_quantity`),
    KEY (`specific_price_rule_id`),
    KEY (`cart_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeproshop_specific_price_priority` (
    `specific_price_priority_id` INT NOT NULL AUTO_INCREMENT ,
    `product_id` INT NOT NULL ,
    `priority` VARCHAR( 80 ) NOT NULL ,
    PRIMARY KEY ( `specific_price_priority_id` , `product_id` ),
    UNIQUE KEY `product_id` (`product_id`)
)  ENGINE=InnoDB  DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeproshop_accessory` (
  `product_1_id` int(10) unsigned NOT NULL,
  `product_2_id` int(10) unsigned NOT NULL,
  KEY `accessory_product` (`product_1_id`,`product_2_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeproshop_manufacturer` (
  `manufacturer_id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(64) NOT NULL,
  `date_add` datetime NOT NULL,
  `date_upd` datetime NOT NULL,
  `published` tinyint(1) NOT NULL default 0,
  PRIMARY KEY (`manufacturer_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeproshop_manufacturer_lang` (
  `manufacturer_id` int(10) unsigned NOT NULL,
  `lang_id` int(10) unsigned NOT NULL,
  `description` text,
  `short_description` text,
  `meta_title` varchar(128) default NULL,
  `meta_keywords` varchar(255) default NULL,
  `meta_description` varchar(255) default NULL,
  PRIMARY KEY (`manufacturer_id`,`lang_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeproshop_manufacturer_shop` (
    `manufacturer_id` INT( 11 ) UNSIGNED NOT NULL ,
    `shop_id` INT( 11 ) UNSIGNED NOT NULL ,
    PRIMARY KEY (`manufacturer_id`, `shop_id`),
    KEY `shop_id` (`shop_id`)
) ENGINE=InnoDB  DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeproshop_attribute_group_shop` (
    `attribute_group_id` INT( 11 ) UNSIGNED NOT NULL ,
    `shop_id` INT( 11 ) UNSIGNED NOT NULL ,
    PRIMARY KEY (`attribute_group_id`, `shop_id`),
    KEY `shop_id` (`shop_id`)
)ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeproshop_stock_mvt` (
  `stock_mvt_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `stock_id` INT(11) UNSIGNED NOT NULL,
  `order_id` INT(11) UNSIGNED DEFAULT NULL,
  `supply_order_id` INT(11) UNSIGNED DEFAULT NULL,
  `stock_mvt_reason_id` INT(11) UNSIGNED NOT NULL,
  `employee_id` INT(11) UNSIGNED NOT NULL,
  `employee_lastname` varchar(32) DEFAULT '',
  `employee_firstname` varchar(32) DEFAULT '',
  `physical_quantity` INT(11) UNSIGNED NOT NULL,
  `date_add` DATETIME NOT NULL,
  `sign` tinyint(1) NOT NULL DEFAULT 1,
  `price_te` DECIMAL(20,6) DEFAULT '0.000000',
  `last_wa` DECIMAL(20,6) DEFAULT '0.000000',
  `current_wa` DECIMAL(20,6) DEFAULT '0.000000',
  `referrer` bigint UNSIGNED DEFAULT NULL,
  PRIMARY KEY (`stock_mvt_id`),
  KEY `stock_id` (`stock_id`),
  KEY `stock_mvt_reason_id` (`stock_mvt_reason_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeproshop_stock_mvt_reason` (
  `stock_mvt_reason_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `sign` tinyint(1) NOT NULL DEFAULT 1,
  `date_add` datetime NOT NULL,
  `date_upd` datetime NOT NULL,
  `deleted` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY (`stock_mvt_reason_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeproshop_stock_mvt_reason_lang` (
    `stock_mvt_reason_id` INT(11) UNSIGNED NOT NULL,
    `lang_id` INT(11) UNSIGNED NOT NULL,
    `name` VARCHAR(255) CHARACTER SET utf8 NOT NULL,
    PRIMARY KEY (`stock_mvt_reason_id`,`lang_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeproshop_stock` (
    `stock_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `warehouse_id` INT(11) UNSIGNED NOT NULL,
    `product_id` INT(11) UNSIGNED NOT NULL,
    `product_attribute_id` INT(11) UNSIGNED NOT NULL,
    `reference`  VARCHAR(32) NOT NULL,
    `ean13`  VARCHAR(13) DEFAULT NULL,
    `upc`  VARCHAR(12) DEFAULT NULL,
    `physical_quantity` INT(11) UNSIGNED NOT NULL,
    `usable_quantity` INT(11) UNSIGNED NOT NULL,
    `price_te` DECIMAL(20,6) DEFAULT '0.000000',
    PRIMARY KEY (`stock_id`),
    KEY `warehouse_id` (`warehouse_id`),
    KEY `product_id` (`product_id`),
    KEY `product_attribute_id` (`product_attribute_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeproshop_attachment` (
  `attachment_id` int(10) unsigned NOT NULL auto_increment,
  `file` varchar(40) NOT NULL,
  `file_name` varchar(128) NOT NULL,
  `file_size` bigint(10) unsigned NOT NULL DEFAULT 0,
  `mime` varchar(128) NOT NULL,
  PRIMARY KEY (`attachment_id`)
)ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeproshop_attachment_lang` (
  `attachment_id` int(10) unsigned NOT NULL auto_increment,
  `lang_id` int(10) unsigned NOT NULL,
  `name` varchar(32) default NULL,
  `description` TEXT,
  PRIMARY KEY (`attachment_id`, `lang_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeproshop_product_attachment` (
  `product_id` int(10) unsigned NOT NULL,
  `attachment_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`product_id`,`attachment_id`)
)ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeproshop_customer` (
  `customer_id` int(10) unsigned NOT NULL auto_increment,
  `shop_group_id` INT(11) UNSIGNED NOT NULL DEFAULT '1',
  `shop_id` INT(11) UNSIGNED NOT NULL DEFAULT '1',
  `title` VARCHAR (5)  NOT NULL,
  `default_group_id` int(10) unsigned NOT NULL DEFAULT '1',
  `lang_id` int(10) unsigned NULL,
  `risk_id` int(10) unsigned NOT NULL DEFAULT '1',
  `company` varchar(64),
  `siret` varchar(14),
  `ape` varchar(5),
  `firstname` varchar(32) NOT NULL,
  `lastname` varchar(32) NOT NULL,
  `email` varchar(128) NOT NULL,
  `passwd` varchar(32) NOT NULL,
  `last_passwd_gen` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `birthday` date default NULL,
  `newsletter` tinyint(1) unsigned NOT NULL default '0',
  `ip_registration_newsletter` varchar(15) default NULL,
  `newsletter_date_add` datetime default NULL,
  `optin` tinyint(1) unsigned NOT NULL default '0',
  `website` varchar(128),
  `outstanding_allow_amount` DECIMAL( 20,6 ) NOT NULL default '0.00',
  `show_public_prices` tinyint(1) unsigned NOT NULL default '0',
  `max_payment_days` int(10) unsigned NOT NULL default '60',
  `secure_key` varchar(32) NOT NULL default '-1',
  `note` text,
  `published` tinyint(1) unsigned NOT NULL default '0',
  `is_guest` tinyint(1) NOT NULL default '0',
  `deleted` tinyint(1) NOT NULL default '0',
  `date_add` datetime NOT NULL,
  `date_upd` datetime NOT NULL,
  PRIMARY KEY (`customer_id`),
  KEY `customer_email` (`email`),
  KEY `customer_login` (`email`,`passwd`),
  KEY `customer_passwd_id` (`customer_id`,`passwd`),
  KEY `shop_group_id` (`shop_group_id`),
  KEY `shop_id` (`shop_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeproshop_customer_group` (
  `customer_id` int(10) unsigned NOT NULL,
  `group_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`customer_id`,`group_id`),
  INDEX customer_login(group_id),
  KEY `customer_id` (`customer_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeproshop_customer_thread` (
  `customer_thread_id` int(11) unsigned NOT NULL auto_increment,
  `shop_id` INT(11) UNSIGNED NOT NULL DEFAULT '1',
  `lang_id` int(10) unsigned NOT NULL,
  `contact_id` int(10) unsigned NOT NULL,
  `customer_id` int(10) unsigned default NULL,
  `order_id` int(10) unsigned default NULL,
  `product_id` int(10) unsigned default NULL,
  `status` enum('open','closed','pending1','pending2') NOT NULL default 'open',
  `email` varchar(128) NOT NULL,
  `token` varchar(12) default NULL,
  `date_add` datetime NOT NULL,
  `date_upd` datetime NOT NULL,
	PRIMARY KEY (`customer_thread_id`),
	KEY `shop_id` (`shop_id`),
	KEY `lang_id` (`lang_id`),
	KEY `contact_id` (`contact_id`),
	KEY `customer_id` (`customer_id`),
	KEY `order_id` (`order_id`),
	KEY `product_id` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeproshop_customer_message` (
  `customer_message_id` int(10) unsigned NOT NULL auto_increment,
  `customer_thread_id` int(11) default NULL,
  `employee_id` int(10) unsigned default NULL,
  `message` text NOT NULL,
  `file_name` varchar(18) DEFAULT NULL,
  `ip_address` int(11) default NULL,
  `user_agent` varchar(128) default NULL,
  `date_add` datetime NOT NULL,
  `date_upd` datetime NOT NULL,
  `private` TINYINT NOT NULL DEFAULT  '0',
  `read` tinyint(1) NOT NULL default '0',
  PRIMARY KEY (`customer_message_id`),
  KEY `customer_thread_id` (`customer_thread_id`),
  KEY `employee_id` (`employee_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeproshop_customer_message_sync_imap` (
  `md5_header` varbinary(32) NOT NULL,
  KEY `md5_header_index` (`md5_header`(4))
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeproshop_orders` (
  `order_id` int(10) unsigned NOT NULL auto_increment,
  `reference` VARCHAR(9),
  `shop_group_id` INT(11) UNSIGNED NOT NULL DEFAULT '1',
  `shop_id` INT(11) UNSIGNED NOT NULL DEFAULT '1',
  `carrier_id` int(10) unsigned NOT NULL,
  `lang_id` int(10) unsigned NOT NULL,
  `customer_id` int(10) unsigned NOT NULL,
  `cart_id` int(10) unsigned NOT NULL,
  `currency_id` int(10) unsigned NOT NULL,
  `address_delivery_id` int(10) unsigned NOT NULL,
  `address_invoice_id` int(10) unsigned NOT NULL,
  `current_status` int(10) unsigned NOT NULL,
  `secure_key` varchar(32) NOT NULL default '-1',
  `payment` varchar(255) NOT NULL,
  `conversion_rate` decimal(13,6) NOT NULL default 1,
  `module` varchar(255) default NULL,
  `recyclable` tinyint(1) unsigned NOT NULL default '0',
  `gift` tinyint(1) unsigned NOT NULL default '0',
  `gift_message` text,
  `mobile_theme` tinyint(1) NOT NULL default 0,
  `shipping_number` varchar(32) default NULL,
  `total_discounts` decimal(17,2) NOT NULL default '0.00',
  `total_discounts_tax_incl` decimal(17,2) NOT NULL default '0.00',
  `total_discounts_tax_excl` decimal(17,2) NOT NULL default '0.00',
  `total_paid` decimal(17,2) NOT NULL default '0.00',
  `total_paid_tax_incl` decimal(17,2) NOT NULL default '0.00',
  `total_paid_tax_excl` decimal(17,2) NOT NULL default '0.00',
  `total_paid_real` decimal(17,2) NOT NULL default '0.00',
  `total_products` decimal(17,2) NOT NULL default '0.00',
  `total_products_wt` DECIMAL(17, 2) NOT NULL default '0.00',
  `total_shipping` decimal(17,2) NOT NULL default '0.00',
  `total_shipping_tax_incl` decimal(17,2) NOT NULL default '0.00',
  `total_shipping_tax_excl` decimal(17,2) NOT NULL default '0.00',
  `carrier_tax_rate` DECIMAL(10, 3) NOT NULL default '0.00',
  `total_wrapping` decimal(17,2) NOT NULL default '0.00',
  `total_wrapping_tax_incl` decimal(17,2) NOT NULL default '0.00',
  `total_wrapping_tax_excl` decimal(17,2) NOT NULL default '0.00',
  `invoice_number` int(10) unsigned NOT NULL default '0',
  `delivery_number` int(10) unsigned NOT NULL default '0',
  `invoice_date` datetime NOT NULL,
  `delivery_date` datetime NOT NULL,
  `valid` int(1) unsigned NOT NULL default '0',
  `date_add` datetime NOT NULL,
  `date_upd` datetime NOT NULL,
  PRIMARY KEY (`order_id`),
  KEY `customer_id` (`customer_id`),
  KEY `cart_id` (`cart_id`),
  KEY `invoice_number` (`invoice_number`),
  KEY `carrier_id` (`carrier_id`),
  KEY `lang_id` (`lang_id`),
  KEY `currency_id` (`currency_id`),
  KEY `address_delivery_id` (`address_delivery_id`),
  KEY `address_invoice_id` (`address_invoice_id`),
  KEY `shop_group_id` (`shop_group_id`),
  KEY `shop_id` (`shop_id`),
  INDEX `date_add`(`date_add`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeproshop_order_invoice` (
  `order_invoice_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `number` int(11) NOT NULL,
  `delivery_number` int(11) NOT NULL,
  `delivery_date` datetime,
  `total_discount_tax_excl` decimal(17,2) NOT NULL DEFAULT '0.00',
  `total_discount_tax_incl` decimal(17,2) NOT NULL DEFAULT '0.00',
  `total_paid_tax_excl` decimal(17,2) NOT NULL DEFAULT '0.00',
  `total_paid_tax_incl` decimal(17,2) NOT NULL DEFAULT '0.00',
  `total_products` decimal(17,2) NOT NULL DEFAULT '0.00',
  `total_products_wt` decimal(17,2) NOT NULL DEFAULT '0.00',
  `total_shipping_tax_excl` decimal(17,2) NOT NULL DEFAULT '0.00',
  `total_shipping_tax_incl` decimal(17,2) NOT NULL DEFAULT '0.00',
  `shipping_tax_computation_method` int(10) unsigned NOT NULL,
  `total_wrapping_tax_excl` decimal(17,2) NOT NULL DEFAULT '0.00',
  `total_wrapping_tax_incl` decimal(17,2) NOT NULL DEFAULT '0.00',
  `note` text,
  `date_add` datetime NOT NULL,
  PRIMARY KEY (`order_invoice_id`),
  KEY `order_id` (`order_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeproshop_order_detail_tax` (
  `order_detail_id` int(11) NOT NULL,
  `tax_id` int(11) NOT NULL,
  `unit_amount` DECIMAL(16, 6) NOT NULL DEFAULT '0.00',
  `total_amount` DECIMAL(16, 6) NOT NULL DEFAULT '0.00',
   PRIMARY KEY (`order_detail_id`),
   KEY `tax_id` (`tax_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeproshop_order_invoice_tax` (
  `order_invoice_id` int(11) NOT NULL,
  `type` varchar(15) NOT NULL,
  `tax_id` int(11) NOT NULL,
  `amount` decimal(10,6) NOT NULL DEFAULT '0.000000',
  KEY `tax_id` (`tax_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeproshop_order_detail` (
  `order_detail_id` int(10) unsigned NOT NULL auto_increment,
  `order_id` int(10) unsigned NOT NULL,
  `order_invoice_id` int(11) default NULL,
  `warehouse_id` int(10) unsigned DEFAULT 0,
  `shop_id` int(11) unsigned NOT NULL,
  `product_id` int(10) unsigned NOT NULL,
  `product_attribute_id` int(10) unsigned default NULL,
  `product_name` varchar(255) NOT NULL,
  `product_quantity` int(10) unsigned NOT NULL default '0',
  `product_quantity_in_stock` int(10) NOT NULL default 0,
  `product_quantity_refunded` int(10) unsigned NOT NULL default '0',
  `product_quantity_return` int(10) unsigned NOT NULL default '0',
  `product_quantity_reinjected` int(10) unsigned NOT NULL default 0,
  `product_price` decimal(20,6) NOT NULL default '0.000000',
  `reduction_percent` DECIMAL(10, 2) NOT NULL default '0.00',
  `reduction_amount` DECIMAL(20, 6) NOT NULL default '0.000000',
  `reduction_amount_tax_incl` DECIMAL(20, 6) NOT NULL default '0.000000',
  `reduction_amount_tax_excl` DECIMAL(20, 6) NOT NULL default '0.000000',
  `group_reduction` DECIMAL(10, 2) NOT NULL default '0.000000',
  `product_quantity_discount` decimal(20,6) NOT NULL default '0.000000',
  `product_ean13` varchar(13) default NULL,
  `product_upc` varchar(12) default NULL,
  `product_reference` varchar(32) default NULL,
  `product_supplier_reference` varchar(32) default NULL,
  `product_weight` DECIMAL(20,6) NOT NULL,
  `tax_computation_method` tinyint(1) unsigned NOT NULL default '0',
  `tax_name` varchar(16) NOT NULL,
  `tax_rate` DECIMAL(10,3) NOT NULL DEFAULT '0.000',
  `ecotax` decimal(21,6) NOT NULL default '0.00',
  `ecotax_tax_rate` DECIMAL(5,3) NOT NULL DEFAULT '0.000',
  `discount_quantity_applied` TINYINT(1) NOT NULL DEFAULT 0,
  `download_hash` varchar(255) default NULL,
  `download_nb` int(10) unsigned default '0',
  `download_deadline` datetime default NULL,
  `total_price_tax_incl` DECIMAL(20, 6) NOT NULL default '0.000000',
  `total_price_tax_excl` DECIMAL(20, 6) NOT NULL default '0.000000',
  `unit_price_tax_incl` DECIMAL(20, 6) NOT NULL default '0.000000',
  `unit_price_tax_excl` DECIMAL(20, 6) NOT NULL default '0.000000',
  `total_shipping_price_tax_incl` DECIMAL(20, 6) NOT NULL default '0.000000',
  `total_shipping_price_tax_excl` DECIMAL(20, 6) NOT NULL default '0.000000',
  `purchase_supplier_price` DECIMAL(20, 6) NOT NULL default '0.000000',
  `original_product_price` DECIMAL(20, 6) NOT NULL default '0.000000',
  PRIMARY KEY (`order_detail_id`),
  KEY `order_detail_order` (`order_id`),
  KEY `product_id` (`product_id`),
  KEY `product_attribute_id` (`product_attribute_id`),
  KEY `order_id_order_detail_id` (`order_id`, `order_detail_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeproshop_order_cart_rule` (
  `order_cart_rule_id` int(10) unsigned NOT NULL auto_increment,
  `order_id` int(10) unsigned NOT NULL,
  `cart_rule_id` int(10) unsigned NOT NULL,
  `order_invoice_id` int(10) unsigned DEFAULT 0,
  `name` varchar(254) NOT NULL,
  `value` decimal(17,2) NOT NULL default '0.00',
  `value_tax_excl` decimal(17,2) NOT NULL default '0.00',
  `free_shipping` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`order_cart_rule_id`),
  KEY `order_id` (`order_id`),
  KEY `cart_rule_id` (`cart_rule_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeproshop_guest` (
  `guest_id` int(10) unsigned NOT NULL auto_increment,
  `operating_system_id` int(10) unsigned default NULL,
  `web_browser_id` int(10) unsigned default NULL,
  `customer_id` int(10) unsigned default NULL,
  `javascript` tinyint(1) default '0',
  `screen_resolution_x` smallint(5) unsigned default NULL,
  `screen_resolution_y` smallint(5) unsigned default NULL,
  `screen_color` tinyint(3) unsigned default NULL,
  `sun_java` tinyint(1) default NULL,
  `adobe_flash` tinyint(1) default NULL,
  `adobe_director` tinyint(1) default NULL,
  `apple_quick_time` tinyint(1) default NULL,
  `real_player` tinyint(1) default NULL,
  `windows_media` tinyint(1) default NULL,
  `accept_language` varchar(8) default NULL,
  `mobile_theme` tinyint(1) NOT NULL default 0,
  PRIMARY KEY (`guest_id`),
  KEY `customer_id` (`customer_id`),
  KEY `operating_system_id` (`operating_system_id`),
  KEY `web_browser_id` (`web_browser_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeproshop_connection` (
  `connection_id` int(10) unsigned NOT NULL auto_increment,
  `shop_group_id` INT(11) UNSIGNED NOT NULL DEFAULT '1',
  `shop_id` INT(11) UNSIGNED NOT NULL DEFAULT '1',
  `guest_id` int(10) unsigned NOT NULL,
  `page_id` int(10) unsigned NOT NULL,
  `ip_address` BIGINT NULL DEFAULT NULL,
  `date_add` datetime NOT NULL,
  `http_referrer` varchar(255) default NULL,
  PRIMARY KEY (`connection_id`),
  KEY `guest_id` (`guest_id`),
  KEY `date_add` (`date_add`),
  KEY `page_id` (`page_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeproshop_connection_page` (
  `connection_id` int(10) unsigned NOT NULL,
  `page_id` int(10) unsigned NOT NULL,
  `time_start` datetime NOT NULL,
  `time_end` datetime default NULL,
  PRIMARY KEY (`connection_id`,`page_id`,`time_start`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeproshop_connection_source` (
  `connection_source_id` int(10) unsigned NOT NULL auto_increment,
  `connection_id` int(10) unsigned NOT NULL,
  `http_referrer` varchar(255) default NULL,
  `request_uri` varchar(255) default NULL,
  `keywords` varchar(255) default NULL,
  `date_add` datetime NOT NULL,
  PRIMARY KEY (`connection_source_id`),
  KEY `connections` (`connection_id`),
  KEY `order_by` (`date_add`),
  KEY `http_referrer` (`http_referrer`),
  KEY `request_uri` (`request_uri`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeproshop_group_shop` (
  `group_id` INT( 11 ) UNSIGNED NOT NULL,
  `shop_id` INT( 11 ) UNSIGNED NOT NULL,
	PRIMARY KEY (`group_id`, `shop_id`),
	KEY `shop_id` (`shop_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeproshop_supplier` (
  `supplier_id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(64) NOT NULL,
  `date_add` datetime NOT NULL,
  `date_upd` datetime NOT NULL,
  `published` tinyint(1) NOT NULL default 0,
  PRIMARY KEY (`supplier_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeproshop_supplier_lang` (
  `supplier_id` int(10) unsigned NOT NULL,
  `lang_id` int(10) unsigned NOT NULL,
  `description` text,
  `short_description` text,
  `meta_title` varchar(128) default NULL,
  `meta_keywords` varchar(255) default NULL,
  `meta_description` varchar(255) default NULL,
  PRIMARY KEY (`supplier_id`,`lang_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeproshop_tag` (
  `tag_id` int(10) unsigned NOT NULL auto_increment,
  `lang_id` int(10) unsigned NOT NULL,
  `name` varchar(32) NOT NULL,
  PRIMARY KEY (`tag_id`),
  KEY `tag_name` (`name`),
  KEY `lang_id` (`lang_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeproshop_product_tag` (
  `product_id` int(10) unsigned NOT NULL,
  `tag_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`product_id`,`tag_id`),
  KEY `tag_id` (`tag_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;


CREATE TABLE IF NOT EXISTS `#__jeproshop_cart` (
  `cart_id` int(10) unsigned NOT NULL auto_increment,
  `shop_group_id` INT(11) UNSIGNED NOT NULL DEFAULT '1',
  `shop_id` INT(11) UNSIGNED NOT NULL DEFAULT '1',
  `carrier_id` int(10) unsigned NOT NULL,
  `delivery_option` TEXT NOT NULL,
  `lang_id` int(10) unsigned NOT NULL,
  `address_delivery_id` int(10) unsigned NOT NULL,
  `address_invoice_id` int(10) unsigned NOT NULL,
  `currency_id` int(10) unsigned NOT NULL,
  `customer_id` int(10) unsigned NOT NULL,
  `guest_id` int(10) unsigned NOT NULL,
  `secure_key` varchar(32) NOT NULL default '-1',
  `recyclable` tinyint(1) unsigned NOT NULL default '1',
  `gift` tinyint(1) unsigned NOT NULL default '0',
  `gift_message` text,
  `mobile_theme` tinyint(1) NOT NULL default 0,
  `allow_seperated_package` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
  `date_add` datetime NOT NULL,
  `date_upd` datetime NOT NULL,
  PRIMARY KEY (`cart_id`),
  KEY `cart_customer` (`customer_id`),
  KEY `address_delivery_id` (`address_delivery_id`),
  KEY `address_invoice_id` (`address_invoice_id`),
  KEY `carrier_id` (`carrier_id`),
  KEY `lang_id` (`lang_id`),
  KEY `currency_id` (`currency_id`),
  KEY `guest_id` (`guest_id`),
  KEY `shop_group_id` (`shop_group_id`),
  KEY `shop_id` (`shop_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeproshop_cart_rule` (
    `cart_rule_id` int(10) unsigned NOT NULL auto_increment,
    `customer_id` int unsigned NOT NULL default 0,
    `date_from` datetime NOT NULL,
    `date_to` datetime NOT NULL,
    `description` text,
    `quantity` int(10) unsigned NOT NULL default 0,
    `quantity_per_user` int(10) unsigned NOT NULL default 0,
    `priority` int(10) unsigned NOT NULL default 1,
    `partial_use` tinyint(1) unsigned NOT NULL default 0,
    `code` varchar(254) NOT NULL,
    `minimum_amount` decimal(17,2) NOT NULL default 0,
    `minimum_amount_tax` tinyint(1) NOT NULL default 0,
    `minimum_amount_currency` int unsigned NOT NULL default 0,
    `minimum_amount_shipping` tinyint(1) NOT NULL default 0,
    `country_restriction` tinyint(1) unsigned NOT NULL default 0,
    `carrier_restriction` tinyint(1) unsigned NOT NULL default 0,
    `group_restriction` tinyint(1) unsigned NOT NULL default 0,
    `cart_rule_restriction` tinyint(1) unsigned NOT NULL default 0,
    `product_restriction` tinyint(1) unsigned NOT NULL default 0,
    `shop_restriction` tinyint(1) unsigned NOT NULL default 0,
    `free_shipping` tinyint(1) NOT NULL default 0,
    `reduction_percent` decimal(5,2) NOT NULL default 0,
    `reduction_amount` decimal(17,2) NOT NULL default 0,
    `reduction_tax` tinyint(1) unsigned NOT NULL default 0,
    `reduction_currency` int(10) unsigned NOT NULL default 0,
    `reduction_product` int(10) NOT NULL default 0,
    `gift_product` int(10) unsigned NOT NULL default 0,
    `gift_product_attribute` int(10) unsigned NOT NULL default 0,
    `highlight` tinyint(1) unsigned NOT NULL default 0,
    `published` tinyint(1) unsigned NOT NULL default 0,
    `date_add` datetime NOT NULL,
    `date_upd` datetime NOT NULL,
    PRIMARY KEY (`cart_rule_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeproshop_cart_rule_lang` (
    `cart_rule_id` int(10) unsigned NOT NULL,
    `lang_id` int(10) unsigned NOT NULL,
    `name` varchar(254) NOT NULL,
    PRIMARY KEY (`cart_rule_id`, `lang_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeproshop_cart_rule_country` (
    `cart_rule_id` int(10) unsigned NOT NULL,
    `country_id` int(10) unsigned NOT NULL,
    PRIMARY KEY (`cart_rule_id`, `country_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeproshop_cart_rule_group` (
    `cart_rule_id` int(10) unsigned NOT NULL,
    `group_id` int(10) unsigned NOT NULL,
    PRIMARY KEY (`cart_rule_id`, `group_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeproshop_cart_rule_carrier` (
    `cart_rule_id` int(10) unsigned NOT NULL,
    `carrier_id` int(10) unsigned NOT NULL,
    PRIMARY KEY (`cart_rule_id`, `carrier_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeproshop_cart_rule_combination` (
    `cart_rule_1_id` int(10) unsigned NOT NULL,
    `cart_rule_2_id` int(10) unsigned NOT NULL,
    PRIMARY KEY (`cart_rule_1_id`, `cart_rule_2_id`),
    KEY `cart_rule_1_id` (`cart_rule_1_id`),
    KEY `cart_rule_2_id` (`cart_rule_2_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeproshop_cart_rule_product_rule_group` (
    `product_rule_group_id` int(10) unsigned NOT NULL auto_increment,
    `cart_rule_id` int(10) unsigned NOT NULL,
    `quantity` int(10) unsigned NOT NULL default 1,
    PRIMARY KEY (`product_rule_group_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeproshop_cart_rule_product_rule` (
    `product_rule_id` int(10) unsigned NOT NULL auto_increment,
    `product_rule_group_id` int(10) unsigned NOT NULL,
    `type` ENUM('products', 'categories', 'attributes', 'manufacturers', 'suppliers','developers') NOT NULL,
    PRIMARY KEY (`product_rule_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeproshop_cart_rule_product_rule_value` (
    `product_rule_id` int(10) unsigned NOT NULL,
    `item_id` int(10) unsigned NOT NULL,
    PRIMARY KEY (`product_rule_id`, `item_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeproshop_cart_cart_rule` (
    `cart_id` int(10) unsigned NOT NULL,
    `cart_rule_id` int(10) unsigned NOT NULL,
    PRIMARY KEY (`cart_id`,`cart_rule_id`),
    KEY (`cart_rule_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeproshop_cart_rule_shop` (
    `cart_rule_id` int(10) unsigned NOT NULL,
    `shop_id` int(10) unsigned NOT NULL,
    PRIMARY KEY (`cart_rule_id`, `shop_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeproshop_cart_product` (
    `cart_id` int(10) unsigned NOT NULL,
    `product_id` int(10) unsigned NOT NULL,
    `address_delivery_id` int(10) UNSIGNED DEFAULT 0,
    `shop_id` int(10) unsigned NOT NULL DEFAULT '1',
    `product_attribute_id` int(10) unsigned default NULL,
    `quantity` int(10) unsigned NOT NULL default '0',
    `date_add` datetime NOT NULL,
    KEY `cart_product_index` (`cart_id`,`product_id`),
    KEY `product_attribute_id` (`product_attribute_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeproshop_referrer` (
  `referrer_id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(64) NOT NULL,
  `passwd` varchar(32) default NULL,
  `http_referrer_regexp` varchar(64) default NULL,
  `http_referrer_like` varchar(64) default NULL,
  `request_uri_regexp` varchar(64) default NULL,
  `request_uri_like` varchar(64) default NULL,
  `http_referrer_regexp_not` varchar(64) default NULL,
  `http_referrer_like_not` varchar(64) default NULL,
  `request_uri_regexp_not` varchar(64) default NULL,
  `request_uri_like_not` varchar(64) default NULL,
  `base_fee` decimal(5,2) NOT NULL default '0.00',
  `percent_fee` decimal(5,2) NOT NULL default '0.00',
  `click_fee` decimal(5,2) NOT NULL default '0.00',
  `date_add` datetime NOT NULL,
  PRIMARY KEY (`referrer_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeproshop_referrer_cache` (
  `connection_source_id` int(11) unsigned NOT NULL,
  `referrer_id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`connection_source_id`, `referrer_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeproshop_referrer_shop` (
  `referrer_id` int(10) unsigned NOT NULL auto_increment,
  `shop_id` int(10) unsigned NOT NULL default '1',
  `cache_visitors` int(11) default NULL,
  `cache_visits` int(11) default NULL,
  `cache_pages` int(11) default NULL,
  `cache_registrations` int(11) default NULL,
  `cache_orders` int(11) default NULL,
  `cache_sales` decimal(17,2) default NULL,
  `cache_reg_rate` decimal(5,4) default NULL,
  `cache_order_rate` decimal(5,4) default NULL,
  PRIMARY KEY (`referrer_id`, `shop_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeproshop_address` (
  `address_id` int(10) unsigned NOT NULL auto_increment,
  `country_id` int(10) unsigned NOT NULL,
  `state_id` int(10) unsigned default NULL,
  `customer_id` int(10) unsigned NOT NULL default '0',
  `manufacturer_id` int(10) unsigned NOT NULL default '0',
  `supplier_id` int(10) unsigned NOT NULL default '0',
  `developer_id` int(10) unsigned NOT NULL default '0',
  `warehouse_id` int(10) unsigned NOT NULL default '0',
  `alias` varchar(32) NOT NULL,
  `company` varchar(64) default NULL,
  `lastname` varchar(32) NOT NULL,
  `firstname` varchar(32) NOT NULL,
  `address1` varchar(128) NOT NULL,
  `address2` varchar(128) default NULL,
  `postcode` varchar(12) default NULL,
  `city` varchar(64) NOT NULL,
  `other` text,
  `phone` varchar(32) default NULL,
  `phone_mobile` varchar(32) default NULL,
  `vat_number` varchar(32) default NULL,
  `dni` varchar(16) DEFAULT NULL,
  `date_add` datetime NOT NULL,
  `date_upd` datetime NOT NULL,
  `published` tinyint(1) unsigned NOT NULL default '1',
  `deleted` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY (`address_id`),
  KEY `address_customer` (`customer_id`),
  KEY `country_id` (`country_id`),
  KEY `state_id` (`state_id`),
  KEY `manufacturer_id` (`manufacturer_id`),
  KEY `supplier_id` (`supplier_id`),
  KEY `developer_id` (`developer_id`),
  KEY `warehouse_id` (`warehouse_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeproshop_feature` (
  `feature_id` int(10) unsigned NOT NULL auto_increment,
  `position` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY (`feature_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeproshop_feature_lang` (
  `feature_id` int(10) unsigned NOT NULL,
  `lang_id` int(10) unsigned NOT NULL,
  `name` varchar(128) default NULL,
  PRIMARY KEY (`feature_id`,`lang_id`),
  KEY (`lang_id`,`name`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeproshop_feature_product` (
  `feature_id` int(10) unsigned NOT NULL,
  `product_id` int(10) unsigned NOT NULL,
  `feature_value_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`feature_id`,`product_id`),
  KEY `feature_value_id` (`feature_value_id`),
  KEY `product_id` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeproshop_feature_value` (
  `feature_value_id` int(10) unsigned NOT NULL auto_increment,
  `feature_id` int(10) unsigned NOT NULL,
  `custom` tinyint(3) unsigned default NULL,
  PRIMARY KEY (`feature_value_id`),
  KEY `feature` (`feature_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeproshop_feature_value_lang` (
  `feature_value_id` int(10) unsigned NOT NULL,
  `lang_id` int(10) unsigned NOT NULL,
  `value` varchar(255) default NULL,
  PRIMARY KEY (`feature_value_id`,`lang_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeproshop_feature_shop` (
    `feature_id` INT(11) UNSIGNED NOT NULL,
    `shop_id` INT(11) UNSIGNED NOT NULL ,
    PRIMARY KEY (`feature_id`, `shop_id`),
    KEY `shop_id` (`shop_id`)
)ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeproshop_product_group_reduction_cache` (
	`product_id` INT UNSIGNED NOT NULL,
	`group_id` INT UNSIGNED NOT NULL,
	`reduction` DECIMAL(4, 3) NOT NULL,
	PRIMARY KEY (`product_id`, `group_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeproshop_product_carrier` (
  `product_id` int(10) unsigned NOT NULL,
  `carrier_reference_id` int(10) unsigned NOT NULL,
  `shop_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`product_id`, `carrier_reference_id`, `shop_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeproshop_product_pack` (
  `product_pack_id` int(10) unsigned NOT NULL,
  `product_item_id` int(10) unsigned NOT NULL,
  `quantity` int(10) unsigned NOT NULL DEFAULT 1,
  PRIMARY KEY (`product_pack_id`,`product_item_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeproshop_warehouse` (
 `warehouse_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
 `currency_id` INT(11) UNSIGNED NOT NULL,
 `address_id` INT(11) UNSIGNED NOT NULL,
 `employee_id` INT(11) UNSIGNED NOT NULL,
 `reference` VARCHAR(32) DEFAULT NULL,
 `name` VARCHAR(45) NOT NULL,
 `management_type` ENUM('WA', 'FIFO', 'LIFO') NOT NULL DEFAULT 'WA',
 `deleted` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY (`warehouse_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeproshop_warehouse_product_location` (
  `warehouse_product_location_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int(11) unsigned NOT NULL,
  `product_attribute_id` int(11) unsigned NOT NULL,
  `warehouse_id` int(11) unsigned NOT NULL,
  `location` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`warehouse_product_location_id`),
  UNIQUE KEY `product_id` (`product_id`,`product_attribute_id`,`warehouse_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeproshop_warehouse_shop` (
  `shop_id` INT(11) UNSIGNED NOT NULL,
  `warehouse_id` INT(11) UNSIGNED NOT NULL,
  PRIMARY KEY (`warehouse_id`, `shop_id`),
  KEY `warehouse_id` (`warehouse_id`),
  KEY `shop_id` (`shop_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeproshop_warehouse_carrier` (
 `carrier_id` INT(11) UNSIGNED NOT NULL,
 `warehouse_id` INT(11) UNSIGNED NOT NULL,
  PRIMARY KEY (`warehouse_id`, `carrier_id`),
  KEY `warehouse_id` (`warehouse_id`),
  KEY `carrier_id` (`carrier_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeproshop_product_category` (
  `category_id` int(10) unsigned NOT NULL,
  `product_id` int(10) unsigned NOT NULL,
  `position` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY (`category_id`,`product_id`),
  INDEX (`product_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeproshop_product_supplier` (
  `product_supplier_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `product_id` int(11) UNSIGNED NOT NULL,
  `product_attribute_id` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `supplier_id` int(11) UNSIGNED NOT NULL,
  `product_supplier_reference` varchar(32) DEFAULT NULL,
  `product_supplier_price_te` decimal(20,6) NOT NULL DEFAULT '0.000000',
  `currency_id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`product_supplier_id`),
  UNIQUE KEY `product_id` (`product_id`,`product_attribute_id`,`supplier_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeproshop_carrier` (
  `carrier_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `reference_id` int(10) unsigned NOT NULL,
  `tax_rules_group_id` int(10) unsigned DEFAULT '0',
  `name` varchar(64) NOT NULL,
  `url` varchar(255) DEFAULT NULL,
  `published` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `deleted` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `shipping_handling` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `range_behavior` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `is_module` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `is_free` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `shipping_external` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `need_range` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `external_module_name` varchar(64) DEFAULT NULL,
  `shipping_method` int(2) NOT NULL DEFAULT '0',
  `position` int(10) unsigned NOT NULL default '0',
  `max_width` int(10) DEFAULT 0,
  `max_height` int(10)  DEFAULT 0,
  `max_depth` int(10)  DEFAULT 0,
  `max_weight` DECIMAL(20,6) DEFAULT 0,
  `grade` int(10)  DEFAULT 0,
  PRIMARY KEY (`carrier_id`),
  KEY `deleted` (`deleted`,`published`),
  KEY `tax_rules_group_id` (`tax_rules_group_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeproshop_carrier_lang` (
  `carrier_id` int(10) unsigned NOT NULL,
  `shop_id` int(11) unsigned NOT NULL DEFAULT '1',
  `lang_id` int(10) unsigned NOT NULL,
  `delay` varchar(128) DEFAULT NULL,
  PRIMARY KEY (`lang_id`,`shop_id`, `carrier_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeproshop_carrier_zone` (
  `carrier_id` int(10) unsigned NOT NULL,
  `zone_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`carrier_id`,`zone_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeproshop_specific_price` (
	`specific_price_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
	`specific_price_rule_id` INT(11) UNSIGNED NOT NULL,
	`cart_id` INT(11) UNSIGNED NOT NULL,
	`product_id` INT UNSIGNED NOT NULL,
	`shop_id` INT(11) UNSIGNED NOT NULL DEFAULT '1',
	`shop_group_id` INT(11) UNSIGNED NOT NULL,
	`currency_id` INT UNSIGNED NOT NULL,
	`country_id` INT UNSIGNED NOT NULL,
	`group_id` INT UNSIGNED NOT NULL,
	`customer_id` INT UNSIGNED NOT NULL,
	`product_attribute_id` INT UNSIGNED NOT NULL,
	`price` DECIMAL(20, 6) NOT NULL,
	`from_quantity` mediumint(8) UNSIGNED NOT NULL,
	`reduction` DECIMAL(20, 6) NOT NULL,
	`reduction_type` ENUM('amount', 'percentage') NOT NULL,
	`from` DATETIME NOT NULL,
	`to` DATETIME NOT NULL,
	PRIMARY KEY (`specific_price_id`),
	KEY (`product_id`, `shop_id`, `currency_id`, `country_id`, `group_id`, `customer_id`, `from_quantity`, `from`, `to`),
	KEY `from_quantity` (`from_quantity`),
	KEY (`specific_price_rule_id`),
	KEY (`cart_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeproshop_specific_price_rule` (
	`specific_price_rule_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`name` VARCHAR(255) NOT NULL,
	`shop_id` int(11) unsigned NOT NULL DEFAULT '1',
	`currency_id` int(10) unsigned NOT NULL,
	`country_id` int(10) unsigned NOT NULL,
	`group_id` int(10) unsigned NOT NULL,
	`from_quantity` mediumint(8) unsigned NOT NULL,
	`price` DECIMAL(20,6),
	`reduction` decimal(20,6) NOT NULL,
	`reduction_type` enum('amount','percentage') NOT NULL,
	`from` datetime NOT NULL,
	`to` datetime NOT NULL,
	PRIMARY KEY (`specific_price_rule_id`),
	KEY `product_id` (`shop_id`,`currency_id`,`country_id`,`group_id`,`from_quantity`,`from`,`to`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeproshop_search_index` (
  `product_id` int(11) unsigned NOT NULL,
  `word_id` int(11) unsigned NOT NULL,
  `weight` smallint(4) unsigned NOT NULL default 1,
  PRIMARY KEY (`word_id`, `product_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeproshop_search_word` (
  `word_id` int(10) unsigned NOT NULL auto_increment,
  `shop_id` int(11) unsigned NOT NULL default 1,
  `lang_id` int(10) unsigned NOT NULL,
  `word` varchar(15) NOT NULL,
  PRIMARY KEY (`word_id`),
  UNIQUE KEY `lang_id` (`lang_id`,`shop_id`, `word`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeproshop_order_status` (
  	`order_status_id` int(10) UNSIGNED NOT NULL auto_increment,
  	`invoice` tinyint(1) UNSIGNED default '0',
	`send_email` tinyint(1) UNSIGNED NOT NULL default '0',
	`module_name` VARCHAR(255) NULL DEFAULT NULL,
  	`color` varchar(32) default NULL,
  	`unremovable` tinyint(1) UNSIGNED NOT NULL,
  	`hidden` tinyint(1) UNSIGNED NOT NULL default '0',
  	`logable` tinyint(1) NOT NULL default '0',
  	`delivery` tinyint(1) UNSIGNED NOT NULL default '0',
  	`shipped` tinyint(1) UNSIGNED NOT NULL default '0',
  	`paid` tinyint(1) UNSIGNED NOT NULL default '0',
  	`deleted` tinyint(1) UNSIGNED NOT NULL default '0',
  	PRIMARY KEY (`order_status_id`),
  	KEY `module_name` (`module_name`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeproshop_order_status_lang` (
  	`order_status_id` int(10) unsigned NOT NULL,
  	`lang_id` int(10) unsigned NOT NULL,
  	`name` varchar(64) NOT NULL,
  	`template` varchar(64) NOT NULL,
  	PRIMARY KEY (`order_status_id`,`lang_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeproshop_carrier_shop` (
	`carrier_id` INT( 11 ) UNSIGNED NOT NULL ,
	`shop_id` INT( 11 ) UNSIGNED NOT NULL ,
	PRIMARY KEY (`carrier_id`, `shop_id`),
  	KEY `shop_id` (`shop_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeproshop_order_slip` (
  `order_slip_id` int(10) unsigned NOT NULL auto_increment,
  `conversion_rate` decimal(13,6) NOT NULL default 1,
  `customer_id` int(10) unsigned NOT NULL,
  `order_id` int(10) unsigned NOT NULL,
  `shipping_cost` tinyint(3) unsigned NOT NULL default '0',
  `amount` DECIMAL(10,2) NOT NULL,
  `shipping_cost_amount` DECIMAL(10,2) NOT NULL,
  `partial` TINYINT(1) NOT NULL,
  `date_add` datetime NOT NULL,
  `date_upd` datetime NOT NULL,
  PRIMARY KEY (`order_slip_id`),
  KEY `order_slip_customer` (`customer_id`),
  KEY `order_id` (`order_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeproshop_order_slip_detail` (
  `order_slip_id` int(10) unsigned NOT NULL,
  `order_detail_id` int(10) unsigned NOT NULL,
  `product_quantity` int(10) unsigned NOT NULL default '0',
  `amount_tax_excl` DECIMAL(10,2) default NULL,
  `amount_tax_incl` DECIMAL(10,2) default NULL,
  PRIMARY KEY (`order_slip_id`,`order_detail_id`)
)  ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeproshop_order_return` (
  `order_return_id` int(10) unsigned NOT NULL auto_increment,
  `customer_id` int(10) unsigned NOT NULL,
  `order_id` int(10) unsigned NOT NULL,
  `state` tinyint(1) unsigned NOT NULL default '1',
  `question` text NOT NULL,
  `date_add` datetime NOT NULL,
  `date_upd` datetime NOT NULL,
  PRIMARY KEY (`order_return_id`),
  KEY `order_return_customer` (`customer_id`),
  KEY `order_id` (`order_id`)
)  ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeproshop_order_return_detail` (
  `order_return_id` int(10) unsigned NOT NULL,
  `order_detail_id` int(10) unsigned NOT NULL,
  `customization_id` int(10) unsigned NOT NULL default '0',
  `product_quantity` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY (`order_return_id`,`order_detail_id`,`customization_id`)
)  ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeproshop_order_return_state` (
  `order_return_state_id` int(10) unsigned NOT NULL auto_increment,
  `color` varchar(32) default NULL,
  PRIMARY KEY (`order_return_state_id`)
)  ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeproshop_order_return_state_lang` (
  `order_return_state_id` int(10) unsigned NOT NULL,
  `lang_id` int(10) unsigned NOT NULL,
  `name` varchar(64) NOT NULL,
  PRIMARY KEY (`order_return_state_id`,`lang_id`)
)  ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeproshop_order_history` (
  `order_history_id` int(10) unsigned NOT NULL auto_increment,
  `employee_id` int(10) unsigned NOT NULL,
  `order_id` int(10) unsigned NOT NULL,
  `order_status_id` int(10) unsigned NOT NULL,
  `date_add` datetime NOT NULL,
  PRIMARY KEY (`order_history_id`),
  KEY `order_history_order` (`order_id`),
  KEY `employee_id` (`employee_id`),
  KEY `order_status_id` (`order_status_id`)
)  ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeproshop_order_message` (
  `order_message_id` int(10) unsigned NOT NULL auto_increment,
  `date_add` datetime NOT NULL,
  PRIMARY KEY (`order_message_id`)
)  ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeproshop_order_message_lang` (
  `order_message_id` int(10) unsigned NOT NULL,
  `lang_id` int(10) unsigned NOT NULL,
  `name` varchar(128) NOT NULL,
  `message` text NOT NULL,
  PRIMARY KEY (`order_message_id`,`lang_id`)
)  ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeproshop_order_payment` (
	`order_payment_id` INT NOT NULL auto_increment,
	`order_reference` VARCHAR(9),
	`currency_id` INT UNSIGNED NOT NULL,
	`amount` DECIMAL(10,2) NOT NULL,
	`payment_method` varchar(255) NOT NULL,
	`conversion_rate` decimal(13,6) NOT NULL DEFAULT 1,
	`transaction_id` VARCHAR(254) NULL,
	`card_number` VARCHAR(254) NULL,
	`card_brand` VARCHAR(254) NULL,
	`card_expiration` CHAR(7) NULL,
	`card_holder` VARCHAR(254) NULL,
	`date_add` DATETIME NOT NULL,
	PRIMARY KEY (`order_payment_id`),
	KEY `order_reference`(`order_reference`)
)  ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeproshop_message` (
  `message_id` int(10) unsigned NOT NULL auto_increment,
  `cart_id` int(10) unsigned default NULL,
  `customer_id` int(10) unsigned NOT NULL,
  `employee_id` int(10) unsigned default NULL,
  `order_id` int(10) unsigned NOT NULL,
  `message` text NOT NULL,
  `private` tinyint(1) unsigned NOT NULL default '1',
  `date_add` datetime NOT NULL,
  PRIMARY KEY (`message_id`),
  KEY `message_order` (`order_id`),
  KEY `cart_id` (`cart_id`),
  KEY `customer_id` (`customer_id`),
  KEY `employee_id` (`employee_id`)
)  ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeproshop_message_readed` (
  `message_id` int(10) unsigned NOT NULL,
  `employee_id` int(10) unsigned NOT NULL,
  `date_add` datetime NOT NULL,
  PRIMARY KEY (`message_id`,`employee_id`)
)  ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeproshop_connection` (
  `connection_id` int(10) unsigned NOT NULL auto_increment,
  `shop_group_id` INT(11) UNSIGNED NOT NULL DEFAULT '1',
  `shop_id` INT(11) UNSIGNED NOT NULL DEFAULT '1',
  `guest_id` int(10) unsigned NOT NULL,
  `page_id` int(10) unsigned NOT NULL,
  `ip_address` BIGINT NULL DEFAULT NULL,
  `date_add` datetime NOT NULL,
  `http_referer` varchar(255) default NULL,
  PRIMARY KEY (`connection_id`),
  KEY `guest_id` (`guest_id`),
  KEY `date_add` (`date_add`),
  KEY `page_id` (`page_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeproshop_connection_page` (
  `connection_id` int(10) unsigned NOT NULL,
  `page_id` int(10) unsigned NOT NULL,
  `time_start` datetime NOT NULL,
  `time_end` datetime default NULL,
  PRIMARY KEY (`connection_id`,`page_id`,`time_start`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;


CREATE TABLE IF NOT EXISTS `#__jeproshop_product_attachment` (
  `product_id` int(10) unsigned NOT NULL,
  `attachment_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`product_id`,`attachment_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeproshop_connection_source` (
  `connection_source_id` int(10) unsigned NOT NULL auto_increment,
  `connection_id` int(10) unsigned NOT NULL,
  `http_referer` varchar(255) default NULL,
  `request_uri` varchar(255) default NULL,
  `keywords` varchar(255) default NULL,
  `date_add` datetime NOT NULL,
  PRIMARY KEY (`connection_source_id`),
  KEY `connection` (`connection_id`),
  KEY `orderby` (`date_add`),
  KEY `http_referer` (`http_referer`),
  KEY `request_uri` (`request_uri`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeproshop_carrier_group` (
  `carrier_id` int(10) unsigned NOT NULL,
  `group_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`carrier_id`,`group_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

 CREATE TABLE IF NOT EXISTS `#__jeproshop_carrier_tax_rules_group_shop` (
	`carrier_id` int( 11 ) unsigned NOT NULL,
	`tax_rules_group_id` int(11) unsigned NOT NULL,
	`shop_id` int(11) unsigned NOT NULL,
	PRIMARY KEY (`carrier_id`, `tax_rules_group_id`, `shop_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeproshop_order_invoice_payment` (
	`order_invoice_id` int(11) unsigned NOT NULL,
	`order_payment_id` int(11) unsigned NOT NULL,
	`order_id` int(11) unsigned NOT NULL,
	PRIMARY KEY (`order_invoice_id`,`order_payment_id`),
	KEY `order_payment` (`order_payment_id`),
	KEY `order_id` (`order_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeproshop_order_carrier` (
  `order_carrier_id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) unsigned NOT NULL,
  `carrier_id` int(11) unsigned NOT NULL,
  `order_invoice_id` int(11) unsigned DEFAULT NULL,
  `weight` DECIMAL(20,6) DEFAULT NULL,
  `shipping_cost_tax_excl` decimal(20,6) DEFAULT NULL,
  `shipping_cost_tax_incl` decimal(20,6) DEFAULT NULL,
  `tracking_number` varchar(64) DEFAULT NULL,
  `date_add` datetime NOT NULL,
  PRIMARY KEY (`order_carrier_id`),
  KEY `order_id` (`order_id`),
  KEY `carrier_id` (`carrier_id`),
  KEY `order_invoice_id` (`order_invoice_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeproshop_category_group` (
  `category_id` int(10) unsigned NOT NULL,
  `group_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`category_id`,`group_id`),
  KEY `category_id` (`category_id`),
  KEY `group_id` (`group_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeproshop_product_attribute_image` (
  `product_attribute_id` int(10) unsigned NOT NULL,
  `image_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`product_attribute_id`,`image_id`),
  KEY `image_id` (`image_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeproshop_address_format` (
  `country_id` int(10) unsigned NOT NULL,
  `format` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`country_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeproshop_scene` (
  `scene_id` int(10) unsigned NOT NULL auto_increment,
  `published` tinyint(1) NOT NULL default '1',
  PRIMARY KEY (`scene_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeproshop_scene_category` (
  `scene_id` int(10) unsigned NOT NULL,
  `category_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`scene_id`,`category_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeproshop_scene_lang` (
  `scene_id` int(10) unsigned NOT NULL,
  `lang_id` int(10) unsigned NOT NULL,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY (`scene_id`,`lang_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeproshop_layered_category`(
  `layered_category_id` int(11) unsigned NOT NULL,
  `shop_id` int(11) unsigned NOT NULL,
  `category_id` int(11) unsigned NOT NULL,
  `value_id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`layered_category_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeproshop_layered_filter`(
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeproshop_layered_filer_shop`(

) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeproshop_layered_friendly`(

) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeproshop_layered_indexable_attribute_group`(
  `attribute_group_id` int(10) unsigned NOT NULL,
  `indexable` tinyint(1) unsigned,
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeproshop_layered_indexable_attribute_group_lang_value`(
  `attribute_id` int(10) unsigned NOT NULL,
  `lang_id` int(11) unsigned NOT NULL,
  `url_name` VARCHAR(20) NOT NULL DEFAULT '',
  `meta_title` VARCHAR(20) NOT NULL DEFAULT '',
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeproshop_layered_indexable_attribute_lang_value`(
  `attribute_id` int(10) unsigned NOT NULL,
  `lang_id` int(11) unsigned NOT NULL,
  `url_name` VARCHAR(20) NOT NULL DEFAULT '',
  `meta_title` VARCHAR(20) NOT NULL DEFAULT '',
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;


CREATE TABLE IF NOT EXISTS `#__jeproshop_layered_indexable_feature`(

) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeproshop_layered_indexable_feature`(

) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeproshop_layered_indexable_feature_value_lang_value`(

) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeproshop_layered_price_index`(

) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeproshop_layered_product_attribute`(

) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeproshop_supply_order` (
  `supply_order_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `supplier_id` INT(11) UNSIGNED NOT NULL,
  `supplier_name` VARCHAR(64) NOT NULL,
  `lang_id` INT(11) UNSIGNED NOT NULL,
  `warehouse_id` INT(11) UNSIGNED NOT NULL,
  `supply_order_status_id` INT(11) UNSIGNED NOT NULL,
  `currency_id` INT(11) UNSIGNED NOT NULL,
  `reference_currency_id` INT(11) UNSIGNED NOT NULL,
  `reference` VARCHAR(64) NOT NULL,
  `date_add` DATETIME NOT NULL,
  `date_upd` DATETIME NOT NULL,
  `date_delivery_expected` DATETIME DEFAULT NULL,
  `total_tax_excluded` DECIMAL(20,6) DEFAULT '0.000000',
  `total_with_discount_tax_excluded` DECIMAL(20,6) DEFAULT '0.000000',
  `total_tax` DECIMAL(20,6) DEFAULT '0.000000',
  `total_tax_included` DECIMAL(20,6) DEFAULT '0.000000',
  `discount_rate` DECIMAL(20,6) DEFAULT '0.000000',
  `discount_value_tax_excluded` DECIMAL(20,6) DEFAULT '0.000000',
  `is_template` tinyint(1) DEFAULT '0',
    PRIMARY KEY (`supply_order_id`),
    KEY `supplier_id` (`supplier_id`),
    KEY `warehouse_id` (`warehouse_id`),
    KEY `reference` (`reference`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeproshop_supply_order_detail` (
  `supply_order_detail_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `supply_order_id` INT(11) UNSIGNED NOT NULL,
  `currency_id` INT(11) UNSIGNED NOT NULL,
  `product_id` INT(11) UNSIGNED NOT NULL,
  `product_attribute_id` INT(11) UNSIGNED NOT NULL,
  `reference`  VARCHAR(32) NOT NULL,
  `supplier_reference`  VARCHAR(32) NOT NULL,
  `name`  varchar(128) NOT NULL,
  `ean13`  VARCHAR(13) DEFAULT NULL,
  `upc`  VARCHAR(12) DEFAULT NULL,
  `exchange_rate` DECIMAL(20,6) DEFAULT '0.000000',
  `unit_price_tax_excluded` DECIMAL(20,6) DEFAULT '0.000000',
  `quantity_expected` INT(11) UNSIGNED NOT NULL,
  `quantity_received` INT(11) UNSIGNED NOT NULL,
  `price_tax_excluded` DECIMAL(20,6) DEFAULT '0.000000',
  `discount_rate` DECIMAL(20,6) DEFAULT '0.000000',
  `discount_value_tax_excluded` DECIMAL(20,6) DEFAULT '0.000000',
  `price_with_discount_tax_excluded` DECIMAL(20,6) DEFAULT '0.000000',
  `tax_rate` DECIMAL(20,6) DEFAULT '0.000000',
  `tax_value` DECIMAL(20,6) DEFAULT '0.000000',
  `price_tax_included` DECIMAL(20,6) DEFAULT '0.000000',
  `tax_value_with_order_discount` DECIMAL(20,6) DEFAULT '0.000000',
  `price_with_order_discount_tax_excluded` DECIMAL(20,6) DEFAULT '0.000000',
    PRIMARY KEY (`supply_order_detail_id`),
    KEY `supply_order_id` (`supply_order_id`),
    KEY `product_id` (`product_id`),
    KEY `product_attribute_id` (`product_attribute_id`),
    KEY `product_product_attribute_id` (`product_id`, `product_attribute_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeproshop_supply_order_history` (
  `supply_order_history_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `supply_order_id` INT(11) UNSIGNED NOT NULL,
  `employee_id` INT(11) UNSIGNED NOT NULL,
  `employee_lastname` varchar(32) DEFAULT '',
  `employee_firstname` varchar(32) DEFAULT '',
  `status_id` INT(11) UNSIGNED NOT NULL,
  `date_add` DATETIME NOT NULL,
    PRIMARY KEY (`supply_order_history_id`),
    KEY `supply_order_id` (`supply_order_id`),
    KEY `employee_id` (`employee_id`),
    KEY `status_id` (`status_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeproshop_supply_order_status` (
  `supply_order_status_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `delivery_note` tinyint(1) NOT NULL DEFAULT 0,
  `editable` tinyint(1) NOT NULL DEFAULT 0,
  `receipt_status` tinyint(1) NOT NULL DEFAULT 0,
  `pending_receipt` tinyint(1) NOT NULL DEFAULT 0,
  `enclosed` tinyint(1) NOT NULL DEFAULT 0,
  `color` VARCHAR(32) DEFAULT NULL,
    PRIMARY KEY (`supply_order_status_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeproshop_supply_order_status_lang` (
  `supply_order_status_id` INT(11) UNSIGNED NOT NULL,
  `lang_id` INT(11) UNSIGNED NOT NULL,
  `name` VARCHAR(128) DEFAULT NULL,
    PRIMARY KEY (`supply_order_status_id`, `lang_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeproshop_supply_order_receipt_history` (
  `supply_order_receipt_history_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `supply_order_detail_id` INT(11) UNSIGNED NOT NULL,
  `employee_id` INT(11) UNSIGNED NOT NULL,
  `employee_lastname` varchar(32) DEFAULT '',
  `employee_firstname` varchar(32) DEFAULT '',
  `supply_order_status_id` INT(11) UNSIGNED NOT NULL,
  `quantity` INT(11) UNSIGNED NOT NULL,
  `date_add` DATETIME NOT NULL,
    PRIMARY KEY (`supply_order_receipt_history_id`),
    KEY `supply_order_detail_id` (`supply_order_detail_id`),
    KEY `supply_order_status_id` (`supply_order_status_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeproshop_scene_product` (
  `scene_id` int(10) unsigned NOT NULL,
  `product_id` int(10) unsigned NOT NULL,
  `x_axis` int(4) NOT NULL,
  `y_axis` int(4) NOT NULL,
  `zone_width` int(3) NOT NULL,
  `zone_height` int(3) NOT NULL,
  PRIMARY KEY (`scene_id`, `product_id`, `x_axis`, `y_axis`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeproshop_attribute_impact` (
  `attribute_impact_id` int(10) unsigned NOT NULL auto_increment,
  `product_id` int(11) unsigned NOT NULL,
  `attribute_id` int(11) unsigned NOT NULL,
  `weight` DECIMAL(20,6) NOT NULL,
  `price` decimal(17,2) NOT NULL,
  PRIMARY KEY (`attribute_impact_id`),
  UNIQUE KEY `product_id` (`product_id`,`attribute_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__jeproshop_product_sale` (
  `product_id` int(10) unsigned NOT NULL,
  `quantity` int(10) unsigned NOT NULL default '0',
  `sale_nbr` int(10) unsigned NOT NULL default '0',
  `date_upd` date NOT NULL,
  PRIMARY KEY (`product_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;