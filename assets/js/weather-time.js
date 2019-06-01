import React from 'react';
import ReactDOM from 'react-dom';
import Results from './components/weather-time/results.js';

class WeatherTime extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            fetching: false,
            results: {}
        };

        // binding is necessary to make `this` work in the callback
        this.handleZipKeyPress = this.handleZipKeyPress.bind(this);
        this.handleSubmit = this.handleSubmit.bind(this);
    }

    componentDidMount() {
        if (initialZip) {
            document.getElementById('zip-input').value = initialZip;
            this.handleSubmit();
        }
    }

    handleSubmit() {
        const zip = document.getElementById('zip-input').value;

        this.setState({
            fetching: true
        });

        fetch('/weather-time', {
            method: 'POST',
            mode: 'same-origin',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                zip: zip
            })
        })
            .then(response => response.json())
            .then(results => {
                this.setState({
                    fetching: false,
                    results: results
                });
            })
            .catch((error) => {
                console.error('Error:', error);
                this.setState({
                    fetching: false,
                    results: {
                        errors: ['fetch error (see console for details)'],
                        zip: zip
                    }
                });
            });
    }

    handleZipKeyPress(event) {
        if (event.key === 'Enter') {
            this.handleSubmit();
        }
    }

    render() {
        return (
            <div>
                <h1>Weather Time!</h1>
                <div id="form-container">
                    <label htmlFor="zip-input">Zip</label>
                    <input id="zip-input" onKeyPress={this.handleZipKeyPress} />
                    <button type="button" onClick={this.handleSubmit}>Go</button>
                </div>
                {(this.state.fetching
                    ? (<div>Fetching Results</div>)
                    : ((this.state.results && Object.keys(this.state.results).length)
                        ? (<Results results={this.state.results} />)
                        : null
                    )
                )}
            </div>
        );
    }
}

ReactDOM.render(<WeatherTime />, document.getElementById('root'));
