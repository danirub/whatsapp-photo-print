<?php

namespace Database\Seeders;

use App\Models\BotMessage;
use Illuminate\Database\Seeder;

class BotMessageSeeder extends Seeder
{
    public function run(): void
    {
        $messages = [
            [
                'key'         => 'welcome',
                'description' => 'Greeting when customer sends first message',
                'message_text' => "👋 Welcome to our Photo Print Service!\n\nPlease send us your photos and when you're done, reply with *finish*.\n\nWe'll print your memories beautifully! 📸",
            ],
            [
                'key'         => 'image_received',
                'description' => 'Confirmation after each image received. Variable: {count}',
                'message_text' => "✅ Photo received! ({count} photo(s) so far)\n\nSend more photos or reply *finish* when you're done.",
            ],
            [
                'key'         => 'no_images_yet',
                'description' => 'When customer types finish but sent no images',
                'message_text' => "⚠️ You haven't sent any photos yet!\n\nPlease send your photos first, then reply *finish* when done.",
            ],
            [
                'key'         => 'select_size',
                'description' => 'Ask to choose print size. Variables: {count}, {sizes}',
                'message_text' => "Great! You've sent *{count} photo(s)* 🎉\n\nPlease choose your print size:\n\n{sizes}\n\nReply with the letter (A, B, or C) of your choice:",
            ],
            [
                'key'         => 'choose_size_button',
                'description' => 'Label for the list picker button',
                'message_text' => 'Choose Size',
            ],
            [
                'key'         => 'invalid_size_selection',
                'description' => 'When customer enters an invalid size',
                'message_text' => "❌ Sorry, that's not a valid option. Please choose from the sizes listed.",
            ],
            [
                'key'         => 'order_summary',
                'description' => 'Order total with confirm prompt. Variables: {count}, {size_label}, {size_name}, {price_each}, {total}',
                'message_text' => "📋 *Order Summary*\n\n📸 Photos: {count}\n📐 Size: {size_label} - {size_name}\n💰 Price per photo: ₪{price_each}\n\n*Total: ₪{total}*\n\nWould you like to confirm this order?",
            ],
            [
                'key'         => 'confirm_yes_button',
                'description' => 'Label for confirm button',
                'message_text' => '✅ Confirm Order',
            ],
            [
                'key'         => 'confirm_no_button',
                'description' => 'Label for cancel/change button',
                'message_text' => '❌ Change Selection',
            ],
            [
                'key'         => 'confirm_prompt',
                'description' => 'When confirmation response is unclear',
                'message_text' => "Please tap *Confirm Order* to proceed or *Change Selection* to start over.",
            ],
            [
                'key'         => 'order_cancelled_reupload',
                'description' => 'When customer cancels and wants to restart',
                'message_text' => "No problem! 😊 Your order has been reset.\n\nPlease send your photos again and reply *finish* when done.",
            ],
            [
                'key'         => 'select_payment',
                'description' => 'Ask how customer wants to pay',
                'message_text' => "💳 How would you like to pay?\n\nChoose your payment method:",
            ],
            [
                'key'         => 'pay_store_button',
                'description' => 'Label for pay in store button',
                'message_text' => '🏪 Pay in Store',
            ],
            [
                'key'         => 'pay_card_button',
                'description' => 'Label for credit card button',
                'message_text' => '💳 Credit Card',
            ],
            [
                'key'         => 'pay_store_confirmation',
                'description' => 'After choosing to pay in store. Variable: {order_id}',
                'message_text' => "🏪 Perfect! Your order *#{order_id}* has been placed.\n\nPlease come to our store to pay and pick up your photos.\n\nThank you for choosing us! 😊",
            ],
            [
                'key'         => 'payment_link_sent',
                'description' => 'Message with PayPlus payment link. Variables: {total}, {url}',
                'message_text' => "💳 Great! Please complete your payment of ₪{total} via the secure link below:\n\n{url}\n\nYour photos will be printed once payment is confirmed. 🖨️",
            ],
            [
                'key'         => 'payment_link_error',
                'description' => 'When payment link creation fails',
                'message_text' => "⚠️ Sorry, we couldn't generate a payment link right now.\n\nPlease contact us directly or try again shortly.",
            ],
            [
                'key'         => 'payment_pending_reminder',
                'description' => 'Reminder when payment is still pending. Variable: {url}',
                'message_text' => "⏳ Your payment is still pending.\n\nComplete your payment here:\n{url}",
            ],
            [
                'key'         => 'payment_confirmed',
                'description' => 'After successful credit card payment. Variable: {order_id}',
                'message_text' => "🎉 Payment confirmed! Thank you!\n\nYour order *#{order_id}* is now being processed.\n\nWe'll have your photos ready soon! 📸",
            ],
            [
                'key'         => 'payment_failed',
                'description' => 'After failed payment attempt',
                'message_text' => "❌ Payment was not completed.\n\nPlease try again or choose to pay in store.",
            ],
            [
                'key'         => 'invalid_payment_choice',
                'description' => 'When payment method selection is unclear',
                'message_text' => "Please choose a payment method from the options above.",
            ],
        ];

        foreach ($messages as $msg) {
            BotMessage::firstOrCreate(['key' => $msg['key']], $msg);
        }
    }
}
