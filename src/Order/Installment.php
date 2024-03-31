<?php
namespace Invays\BogPayments\Order;

use Invays\BogPayments\BogPayments;

class Installment extends BogPayments
{
    private $client_id;
    private $callback_url;
    private $button_text;

    public function __construct(int $client_id)
    {
        $this->client_id = $client_id;
    }

    public function setCallbackUrl(string $callback_url)
    {
        $this->callback_url = $callback_url;
    }
    public function setButtonText(string $button_text)
    {
        $this->button_text = $button_text;
    }

    public function calculator(float $amount)
    {
        $script = "
        <script>
            const button = BOG.SmartButton.render(document.querySelector('.bog-smart-button'), {
                text: '{$this->button_text}',
                onClick: () => {
                    BOG.Calculator.open({
                        amount: {$amount},
                        onClose: () => {
                            // Modal close callback
                        },
                        onRequest: (selected, successCb, closeCb) => {
                            const {
                            amount, month, discount_code
                            } = selected;
                            fetch('{$this->callback_url}', {
                                method: 'POST',
                                body: JSON.stringify(selected)
                            })
                            .then(response => response.json())
                            .then(data => successCb(data.orderId))
                            .catch(err => closeCb());
                        },
                        onComplete: ({redirectUrl}) => {
                            return false;
                        }
                    })
                }
            })
        </script>";
        return '
              <div class="bog-smart-button">
              <script src="https://webstatic.bog.ge/bog-sdk/bog-sdk.js?version=2&client_id=' . $this->client_id . '"></script>
                ' . $script . '
              </div>
        ';
    }
}