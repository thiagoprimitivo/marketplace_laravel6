<?php

namespace App\Http\Controllers;

use App\Payment\PagSeguro\CreditCard;
use App\Payment\PagSeguro\Notification;
use App\Store;
use Illuminate\Http\Request;
use Ramsey\Uuid\Uuid;

class CheckoutController extends Controller
{
    public function index()
    {
        //session()->forget('pagseguro_session_code');

        if (!auth()->check()) {
            return redirect()->route('login');
        }

        if (!session()->has('cart')) {
            return redirect()->route('home');
        }

        $this->makePagSeguroSession();

        $cartItems = array_map(function($product){
            return $product['amount'] * $product['price'];
        }, session()->get('cart'));

        $cartItems = array_sum($cartItems);

        return view('checkout', compact('cartItems'));
    }

    public function process(Request $request)
    {
        try {
            $dataPost = $request->all();
            $user = auth()->user();
            $cartItems = session()->get('cart');
            $stores = array_unique(array_column($cartItems, 'store_id'));
            $reference = 'XPTO'; //Uuid::uuid4()

            $creditCardPayment = new CreditCard($cartItems, $user, $dataPost, $reference);
            $result = $creditCardPayment->doPayment();

            $userOrder = [
                'reference' => $reference,
                'pagseguro_code' => $result->getCode(),
                'pagseguro_status' => $result->getStatus(),
                'itens' => serialize($cartItems)
            ];

            $userOrder = $user->orders()->create($userOrder);

            $userOrder->stores()->sync($stores);

            // Notificar loja, sobre novo pedido
            $store = (new Store())->notifyStoreOwners($stores);

            session()->forget('cart');
            session()->forget('pagseguro_session_code');

            return response()->json([
                'data' => [
                    'status' => true,
                    'message' => 'Pedido criado com sucesso!',
                    'order' => $reference
                ]
            ]);

        } catch (\Exception $e) {

            $message = env('APP_DEBUG') ? $e->getMessage() : 'Erro ao processar pedido!';

            return response()->json([
                'data' => [
                    'status' => false,
                    'message' => $message
                ]
            ], 401);

        }
    }

    public function thanks()
    {
        return view('thanks');
    }

    public function notification()
    {
        $notification = new Notification();

        
    }


    private function makePagSeguroSession()
    {
        if (!session()->has('pagseguro_session_code')) {
            $sessionCode = \PagSeguro\Services\Session::create(\PagSeguro\Configuration\Configure::getAccountCredentials());
            session()->put('pagseguro_session_code', $sessionCode->getResult());
        }
    }
}
