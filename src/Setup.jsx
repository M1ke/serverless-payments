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
			.catch(() => {
				setProcessing(false)
				setError('Something may be wrong with the connection')
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
		<div className="container-shadow" >
			<h2 className="form-title">Set up a payment</h2>
			<form onSubmit={handleSubmit}>
				<div>
					<label for="amount">{`Amount (${currency})`}</label>
					<input name="amount" id="amount" type="number" min="0.01" step="0.01" onChange={changePrice}/>
				</div>
				<div className="mt-2">
					<label for="description">Description</label>
					<input name="description" id="description" onChange={changeDescription} autocomplete="off"/>
				</div>
				<button
					disabled={processing || disabled}
					id="submit"
					className="mt-2 block"
				>
					    <span id="button-text">
					      {processing ? (
						      <div className="spinner" id="spinner"/>
					      ) : (
						      "Create payment"
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
		</div>
	)
}
