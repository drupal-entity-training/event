<?php

namespace Drupal\event_devel\Form;

use Drupal\Component\Render\FormattableMarkup;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\RendererInterface;
use Drupal\Core\Session\AccountInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a form that generates HTTP Basic authentication information.
 */
class BasicAuthInfoForm extends FormBase {

  /**
   * The current user.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $currentUser;

  /**
   * The renderer.
   *
   * @var \Drupal\Core\Render\RendererInterface
   */
  protected $renderer;

  /**
   * Constructs a BasicAuthInfoForm object.
   *
   * @param \Drupal\Core\Session\AccountInterface $current_user
   *   The current user.
   * @param \Drupal\Core\Render\RendererInterface $renderer
   *   The renderer.
   */
  public function __construct(AccountInterface $current_user, RendererInterface $renderer) {
    $this->currentUser = $current_user;
    $this->renderer = $renderer;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('current_user'),
      $container->get('renderer')
    );
  }


  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'event_devel_basic_auth_info_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['username'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Username'),
    ];
    if ($this->currentUser->isAuthenticated()) {
      $form['username']['#default_value'] = $this->currentUser->getAccountName();
    }

    $form['password'] = [
      '#type' => 'password',
      '#title' => $this->t('Password'),
    ];

    $form['actions'] = ['#type' => 'actions'];
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Generate authorization header'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $username = $form_state->getValue('username');
    $password = $form_state->getValue('password');

    $authorization = 'Basic ' . base64_encode("$username:$password");
    $input = [
      '#type' => 'textfield',
      '#title' => $this->t('Authorization value'),
      '#title_display' => 'invisible',
      '#value' => $authorization,
      '#attributes' => ['readonly' => TRUE],
    ];

    drupal_set_message(new FormattableMarkup('<em>@header</em> HTTP header value: <code>@value</code>', [
      '@header' => 'Authorization',
      '@value' => $this->renderer->renderPlain($input),
    ]));
  }


}
