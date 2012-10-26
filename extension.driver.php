<?php

	if(!defined("__IN_SYMPHONY__")) die("<h2>Error</h2><p>You cannot directly access this file</p>");

	/*
	Copyight: Deux Huit Huit 2012
	Copyight: Solutions Nitriques 2011
	License: MIT
	*/
	class extension_save_and_return extends Extension {

		public function getSubscribedDelegates(){
			return array(
				array(
					'page' => '/backend/',
					'delegate' => 'InitaliseAdminPageHead',
					'callback' => 'appendJS'
				),
				array(
					'page' => '/backend/',
					'delegate' => 'AdminPagePreGenerate',
					'callback' => 'appendElement'
				),
				array(
					'page' => '/publish/edit/',
					'delegate' => 'EntryPostEdit',
					'callback' => 'entryPostEdit'
				),
				array(
					'page' => '/publish/new/',
					'delegate' => 'EntryPostCreate',
					'callback' => 'entryPostEdit'
				)
			);
		}
		
		public function entryPostEdit($context) {
			$section = $context['section'];
			$errors = Administration::instance()->Page->_errors;
			
			$isReturn = isset($_POST['fields']['save-and-return-h']) && strlen($_POST['fields']['save-and-return-h']) > 1;
			$isNew = isset($_POST['fields']['save-and-new-h']) && strlen($_POST['fields']['save-and-new-h']) > 1;
			
			//var_dump($context['page']);die();
			
			// if save returned no errors and return ou new button was hit
			if (($isReturn || $isNew) && count($errors) < 1) {
				redirect(sprintf(
					$this->getPath($isNew),
					SYMPHONY_URL,
					$section->get('handle')
				));
			}
		}
		
		public function appendElement($context) {
			// if in edit or new page
			if ($this->isInEditOrNew()) {
				
				$page = $context['oPage'];
				
				$form = $page->Form;
				
				$button_wrap = new XMLELement('div', NULL, array(
					'id' => 'save-and',
					'style' => 'float:right'
				));
				
				
				// @TODO: Get this from static extension
				$static_limit = 2;
				
				if (!$this->isStaticSection() || $static_limit > 1) {
					// add return button in wrapper
					$button_return = $this->createButton('save-and-return', 'Save & return');
					$hidden_return = $this->createHidden('save-and-return-h');
					
					$button_wrap->appendChild($button_return);
					$button_wrap->appendChild($hidden_return);
				}
				
				// if we are not in a static section
				if (!$this->isStaticSection()) {
					// add the new button
					$button_new = $this->createButton('save-and-new', 'Save & new');
					$hidden_new = $this->createHidden('save-and-new-h');
					
					$button_wrap->appendChild($button_new);
					$button_wrap->appendChild($hidden_new);
				}
				
				// add content to the right div
				$div_action = $this->getChildrenWithClass($form, 'div', 'actions');
				
				// if there is no fields, div_action may not be there
				if ($div_action != NULL) {
					$div_action->appendChild($button_wrap);
				}
			}
		}
		
		private function createButton($id, $value) {
			$btn = new XMLElement('input', NULL, array(
				'id' => $id,
				'name' => 'action[save]',
				'value' => __($value),
				'type' => 'submit'
			));
			$btn->setSelfClosingTag(true);
			
			return $btn;
		}
		
		private function createHidden($id) {
			$h = new XMLElement('input', NULL, array(
				'id' => $id,
				'name' => "fields[$id]",
				'type' => 'hidden'
			));
			$h->setSelfClosingTag(true);
			
			return $h;
		}
		
		private function getPath($isNew) {
			if ($isNew) {
				return '%s/publish/%s/new/';
			}
			return '%s/publish/%s/';
		}
		
		private function isInEditOrNew() {
			$c = Administration::instance()->getPageCallback();
			$c = $c['context']['page'];
			
			return ($c == 'edit') || ($c == 'new');
		}
		
		private function getChildrenWithClass($rootElement, $tagName, $className) {
			if (! ($rootElement) instanceof XMLElement) {
				return NULL; // not and XMLElement
			}
			
			// contains the right css class and the right node name
			if (strpos($rootElement->getAttribute('class'), $className) > -1 && $rootElement->getName() == $tagName) {
				return $rootElement;
			}
			
			// recursive search in child elements
			foreach ($rootElement->getChildren() as $child) {
				$res = $this->getChildrenWithClass($child, $tagName, $className);
				
				if ($res != NULL) {
					return $res;
				}
			}
			
			return NULL;
		}

		

		private function isStaticSection($checkLimitReach=true){
			$extman = Symphony::ExtensionManager();
			
			$status = $extman->fetchStatus(array('handle' => 'static_section', 'version' => '1'));
			
			if (in_array(EXTENSION_ENABLED, $status)) {
				$static = Symphony::ExtensionManager()->create('static_section');
				if ($static !== null) {
					return $static->isStaticSection() && ( ($checkLimitReach && $static->isLimitReached()) || !$checkLimitReach );
				}
			}
			
			return false;
		}

		public function appendJS($context){

			if ($this->isInEditOrNew()) {
			
				Administration::instance()->Page->addElementToHead(
					new XMLElement(
						'script',
						"(function($){
							$(function(){
								$('#save-and-return').click(function () {
									$('#save-and-return-h').val('true');
								});
								
								$('#save-and-new').click(function () {
									$('#save-and-new-h').val('true');
								});
							});
						})(jQuery);"
					), time()+100
				);
				
				// add CSS for subsection manager
				Administration::instance()->Page->addElementToHead(
					new XMLElement(
						'style',
						"body.inline.subsection #save-and { display: none; visibility: collapse }",
						array('type' => 'text/css')
					), time()+101
				);
			}
			
		}
	}
	
?>