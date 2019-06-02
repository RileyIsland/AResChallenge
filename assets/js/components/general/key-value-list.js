import Card from 'react-bootstrap/Card';
import Col from 'react-bootstrap/Col';
import ListGroup from 'react-bootstrap/ListGroup';
import React from 'react';
import Row from 'react-bootstrap/Row';

const KeyValueList = (props) => {
    if (!props.items || !Object.keys(props.items).length) {
        return null;
    }
    return (
        <Row>
            <Col>
                <Card>
                    <Card.Header as="h3" className="text-center">
                        {props.title}
                    </Card.Header>
                    <Card.Body>
                        <ListGroup variant="flush">
                            {Object.entries(props.items).map((item, index) => {
                                const itemKey = item[0];
                                const itemValue = item[1];
                                return (
                                    <ListGroup.Item key={index}>
                                        <Row>
                                            <Col className="text-right font-weight-bold"
                                                 xs={6}
                                            >
                                                {itemKey}
                                            </Col>
                                            <Col xs={6}>
                                                <span dangerouslySetInnerHTML={{__html: itemValue}}></span>
                                            </Col>
                                        </Row>
                                    </ListGroup.Item>
                                );
                            })}
                        </ListGroup>
                    </Card.Body>
                </Card>
            </Col>
        </Row>
    );
};

export default KeyValueList;
