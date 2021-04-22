import React, {useEffect, useState} from "react";
import {loadStripe} from "@stripe/stripe-js";
import {Elements} from "@stripe/react-stripe-js";
import Checkout from "./Checkout";
import Setup from "./Setup";
import "./App.css";

// Make sure to call loadStripe outside of a component’s render to avoid
// recreating the Stripe object on every render.
// loadStripe is initialized with a fake API key.
// Sign in to see examples pre-filled with your key.
const promise = loadStripe(process.env.REACT_APP_STRIPE_PUBLIC);

function App(){
	const [id, setId] = useState()

	useEffect(() => {
		const urlParams = new URLSearchParams(window.location.search);
		const urlId = urlParams.get('id');
		if (urlId){
			setId(urlId)
		}
	}, [])

	return (
		<div className="App">
			<header className="App-header">
				<Elements stripe={promise}>
					{id ? <Checkout id={id} setId={setId}/> : <Setup setId={setId}/>}
				</Elements>
			</header>
		</div>
	);
}

export default App;
