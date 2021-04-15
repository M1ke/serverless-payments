import React, {useState, useEffect} from "react"

export default function SetupForm({setId}){
	const [error, setError] = useState(null)
	const [processing, setProcessing] = useState(false)
	const [disabled, setDisabled] = useState(true)
	const [amount, setAmount] = useState(0)
	const [description, setDescription] = useState('')

	useEffect(() => {
		setDisabled(!amount || !description)
	}, [amount, description])

	const handleSubmit = ev => {
		ev.preventDefault();
		setProcessing(true);

		fetch(process.env.REACT_APP_API_URL+"/setup", {
			method: "POST",
			headers: {
				"Content-Type": "application/json"
			},
			body: JSON.stringify({amount, description})
		})
		.then(res => {
			setProcessing(false);
			return res.json()
		}).then(json => {
			const {data, error} = json

			if (error){
				setError(error.description)
				return
			}

			setId(data.id)
			window.history.replaceState({}, 'Make your payment', `?id=${data.id}`)
		})
	}

	const changePrice = ev => {
		setAmount(ev.target.value)
	}

	const changeDescription = ev => {
		setDescription(ev.target.value)
	}

	const currency = process.env.REACT_APP_CURRENCY.toUpperCase()

	return (
		<form id="payment-form" onSubmit={handleSubmit}>
			<fieldset>
				<div>
					<label for="amount">{`Amount (${currency})`}</label>
					<input name="amount" id="amount" type="number" min="0.01" step="0.01" onChange={changePrice}/>
				</div>
				<div>
					<label for="description">Description</label>
					<input name="description" id="description" onChange={changeDescription}/>
				</div>
				<button
					disabled={processing || disabled}
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
			</fieldset>
		</form>
	)
}
