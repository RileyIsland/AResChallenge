import Alert from 'react-bootstrap/Alert';
import ListGroup from 'react-bootstrap/ListGroup';
import React from 'react';

const Errors = (props) => {
    return (
        <Alert variant="danger" className="bg-transparent">
            <Alert.Heading className="text-center">
                Error{ props.errors.length > 1 ? 's' : '' }
            </Alert.Heading>
            <ListGroup>
                {props.errors.map((error, index) => {
                    return (
                        <ListGroup.Item key={index} variant="danger">
                            {error}
                        </ListGroup.Item>
                    );
                })}
            </ListGroup>
        </Alert>
    );
};

export default Errors;
