import Col from 'react-bootstrap/Col';
import Errors from '../general/errors.js';
import KeyValueList from '../general/key-value-list.js';
import React from 'react';
import Row from 'react-bootstrap/Row';
import WeatherReports from './weather-reports.js';

const Results = (props) => (
    <Row>
        <Col>
            <h2 className="text-center">Results for {props.results.zip}</h2>
            {(props.results.errors && props.results.errors.length
                ? (<Errors errors={props.results.errors} />)
                : (
                    <Row>
                        <Col>
                            <Row>
                                <Col className="mb-3">
                                    <WeatherReports weatherReports={props.results.weather_reports} />
                                </Col>
                            </Row>
                            <Row>
                                <Col className="mb-3" xs={12} md={6}>
                                    <KeyValueList title="Location Data"
                                                  items={props.results.location_data}
                                    />
                                </Col>
                                <Col className="mb-3" xs={12} md={6}>
                                    <KeyValueList title="General Weather"
                                                  items={props.results.general_weather}
                                    />
                                </Col>
                            </Row>
                        </Col>
                    </Row>
                )
            )}
        </Col>
    </Row>
);

export default Results;
