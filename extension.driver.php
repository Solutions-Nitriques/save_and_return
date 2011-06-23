<?php

	if(!defined("__IN_SYMPHONY__")) die("<h2>Error</h2><p>You cannot directly access this file</p>");

	/*
	Copyight: Solutions Nitriques 2011
	License: MIT
	*/
	class extension_save_and_return extends Extension {

		public function about() {
			return array(
				'name'			=> 'Save and Return',
				'version'		=> '1.0',
				'release-date'	=> '2011-06-23',
				'author'		=> array(
					'name'			=> 'Solutions Nitriques',
					'website'		=> 'http://www.nitriques.com/open-source/',
					'email'			=> 'nico (at) nitriques.com'
				),
				'description'	=> 'Permits the user to save and return to the list of a section',
				'compatibility' => array(
					'2.2.1' => true,
					'2.2' => true
				)
	 		);
		}

		public function getSubscribedDelegates(){
			return array(
				array(
					'page' => '/backend/',
					'delegate' => 'InitaliseAdminPageHead',
					'callback' => 'appendJS'
				),
				/*array(
					'page' => '/backend/',
					'delegate' => 'AppendElementBelowView',
					'callback' => ''
				),*/
				array(
					'page' => '/publish/edit/',
					'delegate' => 'EntryPostEdit',
					'callback' => 'entryPostEdit'
				)/*,
				array(
					'page' => '/publish/new/',
					'delegate' => 'EntryPostCreate',
					'callback' => 'entryPostEdit'
				)*/
			);
		}
		
		public function entryPostEdit($context) {
			var_dump($context);
			die();
		}

		public function appendJS($context){
			$c = Administration::instance()->getPageCallback();
			$c = $c['pageroot'];
			/*if ($c == '/blueprints/sections/') {
				Administration::instance()->Page->addElementToHead(
					new XMLElement(
						'script',
						"(function($){
							$(function(){
								$('#fields-duplicator .controls > .collapser:first').trigger('click.duplicator');
							});
						})(jQuery);"
					), time()+1
				);
			}*/
			
			/*Administration::instance()->Page->addElementToHead(
				new XMLElement(
					'script',
					"alert('yeah')"
				), time()+1
			);*/
		}
	}
	
?>