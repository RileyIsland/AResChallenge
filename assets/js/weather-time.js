import Button from 'react-bootstrap/Button';
import Container from 'react-bootstrap/Container';
import Col from 'react-bootstrap/Col';
import Form from 'react-bootstrap/Form';
import React from 'react';
import ReactDOM from 'react-dom';
import Results from './components/weather-time/results.js';
import Row from 'react-bootstrap/Row';
import Spinner from 'react-bootstrap/Spinner';

class WeatherTime extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            fetching: false,
            results: {},
            requestCounter: 0
        };

        // binding is necessary to make `this` work in callbacks
        this.handleFormSubmit = this.handleFormSubmit.bind(this);
        this.hasResults = this.hasResults.bind(this);
    }

    componentDidMount() {
        if (initialZip) {
            document.getElementById('zip-input').value = initialZip;
            this.handleFormSubmit();
        }
    }

    handleFormSubmit(event) {
        if (event) {
            event.preventDefault();
        }

        const zip = document.getElementById('zip-input').value;
        const requestCounter = this.state.requestCounter + 1;

        this.setState({
            fetching: true,
            requestCounter: requestCounter
        });

        fetch('/', {
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
                if (results.zip === zip &&
                    requestCounter === this.state.requestCounter
                ) {
                    this.setState({
                        fetching: false,
                        results: {
                            errors: results.errors,
                            general_weather: results.general_weather,
                            location_data: results.location_data,
                            weather_reports: results.weather_reports,
                            zip: results.zip
                        }
                    });
                }
            })
            .catch((error) => {
                if (requestCounter === this.state.requestCounter) {
                    console.error('Error:', error);
                    this.setState({
                        fetching: false,
                        results: {
                            errors: ['fetch error (see console for details)'],
                            zip: zip
                        }
                    });
                }
            });
    }

    hasResults() {
        return this.state.results && Object.keys(this.state.results).length;
    }

    render() {
        return (
            <Container>
                <Row>
                    <Col>
                        <h1 className="text-center">Weather Time!</h1>
                    </Col>
                </Row>
                <Row>
                    <Col>
                        <Form onSubmit={this.handleFormSubmit}>
                            <Form.Group as={Row} controlId="zip-input">
                                <Form.Label column className="text-right" xs={1}>
                                    Zip
                                </Form.Label>
                                <Col xs={11}>
                                    <Form.Control placeholder="Enter zip" />
                                </Col>
                            </Form.Group>
                            <Form.Group as={Row}>
                                <Col xs={{offset: 1}}>
                                    <Button type="submit">Go</Button>
                                </Col>
                            </Form.Group>
                        </Form>
                    </Col>
                </Row>
                {(this.state.fetching
                    ? (
                        <Row>
                            <Col className="text-center">
                                <Spinner animation="border" />
                            </Col>
                        </Row>
                    )
                    : ((this.hasResults())
                        ? (
                            <Row>
                                <Col>
                                    <Results results={this.state.results} />
                                </Col>
                            </Row>
                        )
                        : null
                    )
                )}
            </Container>
        );
    }
}

ReactDOM.render(<WeatherTime />, document.getElementById('root'));
