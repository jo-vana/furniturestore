<?php
/**
 * Created by PhpStorm.
 * User: veus
 * Date: 11/24/17
 * Time: 9:54 AM
 */

namespace Drupal\newsletter\Form;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

class NewsletterForm extends FormBase
{
    public function getFormId()
    {
        return 'newsletter_form_block';

    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state)
    {
        $form['details'] = [
            '#markup' => '<h3>Get Updates</h3>',
        ];
        $form['subtitle'] = [
            '#markup' => '<p class="subtittle">Subscribe our newsletter to get the best stories into your inbox!</p>',
        ];
        $form['email'] = array(
            '#type' => 'email',
            '#placeholder' => 'E-mail',
            '#size'  => 32,
            '#required' => false,
        );

        $form['actions'] = array(
            '#type' => 'submit',
            '#value' => t('Subscribe'),

            '#ajax' => array(
                'callback' => '::ajaxFormSubmit',
                'event' => 'click',
                'progress' => array(
                    'type' => 'throbber',
                    'message' => 'Checking your Email. Please wait!',
                ),
            ),
            $form_state->setCached(FALSE),
        );
        $form[ 'message' ] = [
            '#type'       => 'container',
            '#attributes' => [
                'id' => 'newsletter-message',
            ],
        ];

        $form['description'] = array(
            '#markup' => '<p class="spam">Dont\'t worry we hate spams</p>',
        );

        return $form;
    }

    /**
     * {@inheritdoc}
     */
    public function validateForm(array &$form, FormStateInterface $form_state)
    {
        $email = $form_state->getValue('title');
        if (!\Drupal::service('email.validator')->isValid($email)) {
            $form_state->setErrorByName('title', $this->t('The email address appears to be invalid.'));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state)
    {

    }

    public function ajaxFormSubmit(array $form, FormStateInterface $form_state)
    {
        $values = $form_state->getValue('email');

        $email = \Drupal::database()->select('newsletter', 'n');
        $email->condition('n.email', $values, '=');
        $email->addField('n', 'email');
        $emails = $email->execute()->fetchAll();

        if (empty($emails)) {
            $insert = \Drupal::database()->insert('newsletter');
            $insert->fields([
                'email',
                'date',
            ]);
            $insert->values([
                $values,
                date('F d, Y'),
            ]);

            $insert->execute();

            $item = [
                '#type'       => 'container',
                '#attributes' => [
                    'id'    => 'newsletter-message',
                    'class' => 'success',
                ],
                '#markup'     => "Thank You!",
            ];

        } else {
            $item = [
                '#type'       => 'container',
                '#attributes' => [
                    'id'    => 'newsletter-message',
                    'class' => 'fail',
                ],
                '#markup'     => "Email already exists in our database",
            ];
        }
        $renderer = \Drupal::service( 'renderer' );
        $response = new AjaxResponse();
        $item = $renderer->render( $item );
        $response->addCommand( new ReplaceCommand( '#newsletter-message', $item ) );

        return $response;
    }
}

