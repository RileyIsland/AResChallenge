import React from 'react';

const KeyValueList = (props) => {
    if (!props.items || !Object.keys(props.items).length) {
        return null;
    }
    return (
        <div>
            <h3>{props.title}:</h3>
            <ul>
                {Object.entries(props.items).map((item, index) => {
                    const itemKey = item[0];
                    const itemValue = item[1];
                    return (
                        <li key={index}>
                            <strong>{itemKey}:</strong>
                            {' '}
                            <span dangerouslySetInnerHTML={{__html: itemValue}}></span>
                        </li>
                    );
                })}
            </ul>
        </div>
    );
};

export default KeyValueList;
