import Card from 'react-bootstrap/Card';
import Col from 'react-bootstrap/Col';
import React from 'react';
import Row from 'react-bootstrap/Row';

const WeatherReports = (props) => {
    if (!props.weatherReports || !Object.keys(props.weatherReports).length) {
        return null;
    }
    return (
        <Row>
            <Col>
                <Card className="text-center">
                    <Card.Header as="h3">
                        Weather Report{ props.weatherReports.length > 1 ? 's' : '' }
                    </Card.Header>
                    <Card.Body>
                        <Row className="justify-content-center">
                            {Object.values(props.weatherReports).map((value, index) => (
                                <Col key={index}
                                     className="mb-3"
                                     xs={4} md={3} lg={2}
                                 >
                                    <Card>
                                        <Card.Header as="h5">{value.main}</Card.Header>
                                        <Card.Img src={'//openweathermap.org/img/w/' + value.icon + '.png'} />
                                        <Card.Body>
                                            <Card.Text>{value.description}</Card.Text>
                                        </Card.Body>
                                    </Card>
                                </Col>
                            ))}
                        </Row>
                    </Card.Body>
                </Card>
            </Col>
        </Row>
    );
};

export default WeatherReports;
