import React, {useState, useEffect} from "react";
import {
	CardElement,
	useStripe,
	useElements
} from "@stripe/react-stripe-js";

export default function CheckoutForm({ id, setId }){
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
		return <p>Setting up the payment&hellip;</p>
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

	const reset = () => {
		setId(null)
	}

	return (
		<div className="container-shadow">
			<h2 className="form-title">{succeeded ? 'Thanks for your custom' : 'Complete the payment'}</h2>
			{succeeded ? <p className="mt-2 p-2 bg-green-600 text-white text-center">
					{`Your payment of ${payment.amount} (${payment.currency}) for: ${payment.description} is complete`}
				</p> :
				<>
					<p className="mb-2">{`Ready to make a payment of ${payment.amount} (${payment.currency}) for: ${payment.description}`}</p>
					<form id="payment-form" onSubmit={handleSubmit}>
						<CardElement id="card-element" options={cardStyle} onChange={handleChange}/>
						<button
							disabled={processing || disabled || succeeded}
							id="submit"
							className="mt-2"
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
							<div className="mt-2 p-2 bg-red-400 text-white text-center" role="alert">
								{error}
							</div>
						)}
					</form>
				</>}
			<p className="mt-2"><a href="#" onClick={reset} className="text-blue-500 hover:text-blue-700 font-bold">Set up a new payment</a></p>
		</div>
	);
}
