import React, {useState, useEffect} from "react";
import {
	CardElement,
	useStripe,
	useElements
} from "@stripe/react-stripe-js";

export default function CheckoutForm({id}){
	const [succeeded, setSucceeded] = useState(false);
	const [error, setError] = useState(null);
	const [processing, setProcessing] = useState('');
	const [disabled, setDisabled] = useState(true);
	const [payment, setPayment] = useState(null);
	const stripe = useStripe();
	const elements = useElements();

	useEffect(() => {
		// Create PaymentIntent as soon as the page loads
		fetch(`${process.env.REACT_APP_API_URL}/start?id=${id}`, {
			method: "POST",
			headers: {
				"Content-Type": "application/json"
			},
		})
			.then(res => {
				console.log('Stripe create request', res)
				return res.json();
			})
			.then(res => {
				setPayment(res.data)
			});
	}, [id]);

	if (!payment){
		return <p>Stripe is loading&hellip;</p>
	}

	const cardStyle = {
		style: {
			base: {
				color: "#32325d",
				fontFamily: 'Arial, sans-serif',
				fontSmoothing: "antialiased",
				fontSize: "16px",
				"::placeholder": {
					color: "#32325d"
				}
			},
			invalid: {
				color: "#fa755a",
				iconColor: "#fa755a"
			}
		}
	};

	const handleChange = async (event) => {
		// Listen for changes in the CardElement
		// and display any errors as the customer types their card details
		setDisabled(event.empty);
		setError(event.error ? event.error.message : "");
	};

	const handleSubmit = async ev => {
		ev.preventDefault();
		setProcessing(true);
		const {clientSecret} = payment

		const payload = await stripe.confirmCardPayment(clientSecret, {
			payment_method: {
				card: elements.getElement(CardElement)
			}
		});

		if (payload.error){
			setError(`Payment failed ${payload.error.message}`);
			setProcessing(false);
		}
		else {
			setError(null);
			setProcessing(false);
			setSucceeded(true);
		}
	};

	return (
		<form id="payment-form" onSubmit={handleSubmit}>
			<p>{`Ready to make a payment of ${payment.amount} (${payment.currency}) for: ${payment.description}`}</p>
			<CardElement id="card-element" options={cardStyle} onChange={handleChange}/>
			<button
				disabled={processing || disabled || succeeded}
				id="submit"
			>
        <span id="button-text">
          {processing ? (
	          <div className="spinner" id="spinner"/>
          ) : (
	          "Pay now"
          )}
        </span>
			</button>
			{/* Show any error that happens when processing the payment */}
			{error && (
				<div className="card-error" role="alert">
					{error}
				</div>
			)}
			{/* Show a success message upon completion */}
			<p className={succeeded ? "result-message" : "result-message hidden"}>
				Payment succeeded, see the result in your
				<a
					href={`https://dashboard.stripe.com/test/payments`}
				>
					{" "}
					Stripe dashboard.
				</a> Refresh the page to pay again.
			</p>
		</form>
	);
}
