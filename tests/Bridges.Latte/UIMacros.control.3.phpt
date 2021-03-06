<?php

/**
 * Test: {control ...}
 */

declare(strict_types=1);

use Nette\Bridges\ApplicationLatte\UIMacros;
use Tester\Assert;


require __DIR__ . '/../bootstrap.php';


$latte = new Latte\Engine;
$latte->setLoader(new Latte\Loaders\StringLoader);
UIMacros::install($latte->getCompiler());
$latte->addProvider('uiControl', new class {
	public function render()
	{
		echo '<>&amp;';
	}

	public function __call($name, $args)
	{
		return new self;
	}
});

Assert::error(function () use ($latte) {
	$latte->renderToString('<div {control x}');
}, E_USER_WARNING, 'Filters: unable to convert content type HTML to HTMLTAG');

Assert::same(
	'<div title="&lt;&gt;&amp;">',
	$latte->renderToString('<div title="{control x}">')
);

Assert::error(function () use ($latte) {
	$latte->renderToString('<style> {control x} </style>');
}, E_USER_WARNING, 'Filters: unable to convert content type HTML to HTMLCSS');
