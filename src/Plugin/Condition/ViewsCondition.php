<?php

namespace Drupal\views_condition\Plugin\Condition;

use Drupal\Core\Condition\ConditionPluginBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Routing\CurrentRouteMatch;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Condition\ConditionInterface;

/**
 * Provides a 'Views' condition to enable on a views page.
 *
 * @Condition(
 *   id = "views_condition",
 *   label = @Translation("Views")
 * )
 */
class ViewsCondition extends ConditionPluginBase implements ConditionInterface, ContainerFactoryPluginInterface {

  /**
   * Used to check if we're on a views page.
   *
   * @var \Drupal\Core\Routing\CurrentRouteMatch
   */
  private $routeMatch;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('current_route_match')
    );
  }

  /**
   * Creates a new ViewsCondition object.
   *
   * @param array $configuration
   *   The plugin configuration, i.e. an array with configuration values keyed
   *   by configuration option name. The special key 'context' may be used to
   *   initialize the defined contexts by setting it to an array of context
   *   values keyed by context names.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param CurrentRouteMatch $route_match
   *   Route match.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, CurrentRouteMatch $route_match) {
    $this->routeMatch = $route_match;
    parent::__construct($configuration, $plugin_id, $plugin_definition);

  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $form['view_pages'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Apply to view pages.'),
      '#default_value' => $this->configuration['view_pages'],
    ];
    $form['#attached']['library'][] = 'views_condition/views_condition';
    return parent::buildConfigurationForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    $this->configuration['view_pages'] = $form_state->getValue('view_pages');
    parent::submitConfigurationForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return ['view_pages' => 0] + parent::defaultConfiguration();
  }

  /**
   * {@inheritdoc}
   */
  public function evaluate() {

    if (!empty($this->configuration['view_pages'])) {
      // DON'T Display on view pages.
      if ($this->isNegated()) {
        if ($this->onViewsPage()) {
          return TRUE;
        }
      }

      // Display on view pages.
      else {
        if ($this->onViewsPage()) {
          return TRUE;
        }
      }

    }
    elseif ($this->isNegated() && !$this->onViewsPage()) {
      return TRUE;
    }

    return FALSE;
  }

  /**
   * Use current route to determine if its on a view page.
   *
   * @return bool
   *   If on a view page.
   */
  private function onViewsPage() {
    return (bool) $this->routeMatch->getParameter('view_id');
  }

  /**
   * {@inheritdoc}
   */
  public function summary() {
    return t('Applied to view pages.');
  }

}
