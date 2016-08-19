<?php

/**
 * @file
 * Definition of Drupal\views_date_sort_by_time\Plugin\views\style\DateSortByTime.
 */

namespace Drupal\views_date_sort_by_time\Plugin\views\sort;

use Drupal\Core\Form\FormStateInterface;
use Drupal\views\Plugin\views\sort\SortPluginBase;

/**
 * Basic sort handler for dates.
 *
 * This handler enables sorting by time instead of complete date.
 *
 * @ingroup views_sort_handlers
 *
 * @ViewsSort("date_sort_by_time")
 */
class DateSortByTime extends SortPluginBase {

  /**
   * Override to account for dates stored as strings.
   * @see Drupal\datetime\Plugin\views\sort\Date
   *
   * We have to do that here, because @see Drupal\views\Plugin\views\sort\Date adds granularity.
   */
  public function getDateField() {
    // Return the real field, since it is already in string format.
    return "$this->tableAlias.$this->realField";
  }

  /**
   * {@inheritdoc}
   *
   * Overridden in order to pass in the string date flag.
   * @see Drupal\datetime\Plugin\views\sort\Date
   *
   * We have to do that here, because @see Drupal\views\Plugin\views\sort\Date adds granularity.
   */
  public function getDateFormat($format) {
    return $this->query->getDateFormat($this->getDateField(), $format, TRUE);
  }

  protected function defineOptions() {
    $options = parent::defineOptions();

    $options['sort_by_time'] = array('default' => 'time');

    return $options;
  }

  public function buildOptionsForm(&$form, FormStateInterface $form_state) {
    parent::buildOptionsForm($form, $form_state);
    $form['sort_by_time'] = array(
      '#type' => 'radios',
      '#title' => $this->t('Sort by'),
      '#options' => array(
        'date' => $this->t('Date'),
        'time' => $this->t('Time'),
      ),
      '#description' => $this->t('Sort dates by time or by date.'),
      '#default_value' => $this->options['sort_by_time'],
    );
  }

  /**
   * Called to add the sort to a query.
   */
  public function query() {
    $this->ensureMyTable();
    switch ($this->options['sort_by_time']) {
      case 'date':
        $formula = $this->getDateFormat('YmdHi');
        break;
      case 'time':
        $formula = $this->getDateFormat('Hi');
        break;
    }

    // Add the field.
    $this->query->addOrderBy(NULL, $formula, $this->options['order'], $this->tableAlias . '_' . $this->field . '_' . $this->options['sort_by_time']);
  }

}
