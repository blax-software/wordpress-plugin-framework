<?php

namespace Blax\Wordpress\Extendables\Pages;

abstract class SubMenu
{
	const PAGE_PARENT = 'options-general.php';
	const PAGE_TITLE = 'Blax Subpage';
	const MENU_TITLE = 'Blax Subpage';
	const CAPABILITY = 'manage_options';
	const SLUG = null;
	const POSITION = 1;

	public function __construct()
	{
		add_action('admin_menu', function () {
			add_submenu_page(
				static::PAGE_PARENT,
				static::PAGE_TITLE,
				static::MENU_TITLE,
				static::CAPABILITY,
				static::SLUG ?? str_replace(' ', '_', strtolower(static::PAGE_TITLE)),
				function () {
					echo '<div class="wrap">';
					$this->render();
					echo '</div>';
				},
				static::POSITION,
			);
		});
	}

	abstract public function render();
}
