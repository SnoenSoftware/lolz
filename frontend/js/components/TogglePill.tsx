import React, { useState } from "react";
import styled from "styled-components";

const SliderContainer = styled.label`
    position: absolute;
    top: 1em;
    right: 1em;
    display: inline-block;
    width: 60px;
    height: 34px;
`;

const SneakyCheckbox = styled.input`
    opacity: 0;
    width: 0;
    height: 0;

    :checked + span {
        background-color: #2196f3;
    }
    :focus + span {
        box-shadow: 0 0 1px #2196f3;
    }
    :checked + span:before {
        -webkit-transform: translateX(26px);
        -ms-transform: translateX(26px);
        transform: translateX(26px);
    }
`;

const Slider = styled.span`
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #ccc;
    -webkit-transition: 0.4s;
    transition: 0.4s;
    border-radius: 34px;

    :before {
        position: absolute;
        content: "";
        height: 26px;
        width: 26px;
        left: 4px;
        bottom: 4px;
        background-color: white;
        -webkit-transition: 0.4s;
        transition: 0.4s;
        border-radius: 50%;
    }
`;

export const TogglePill: React.FC<{
    initialState: boolean;
    toggleFunc: (event: React.ChangeEvent<HTMLInputElement>) => void;
}> = ({ initialState = false, toggleFunc }) => {
    const [checked, setChecked] = useState(initialState);

    const changeFunc = (event: React.ChangeEvent<HTMLInputElement>) => {
        setChecked(event.target.checked);
        toggleFunc(event);
    };

    return (
        <SliderContainer>
            <SneakyCheckbox
                type="checkbox"
                onChange={changeFunc}
                checked={checked}
            />
            <Slider />
        </SliderContainer>
    );
};
