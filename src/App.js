import React from "react";
import { loadStripe } from "@stripe/stripe-js";
import { Elements } from "@stripe/react-stripe-js";
import Checkout from "./Checkout";
import "./App.css";

// Make sure to call loadStripe outside of a component’s render to avoid
// recreating the Stripe object on every render.
// loadStripe is initialized with a fake API key.
// Sign in to see examples pre-filled with your key.
const promise = loadStripe("pk_test_TYooMQauvdEDq54NiTphI7jx");

function App() {
  return (
    <div className="App">
      <header className="App-header">
        <Elements stripe={promise}>
          <Checkout />
        </Elements>
      </header>
    </div>
  );
}

export default App;
