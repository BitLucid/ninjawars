/* eslint-disable max-len */
/* eslint-disable import/extensions */
/* eslint-disable indent */
/* global $ */

import api from './api.js';
import { logger } from './utils.js';

const render = () => {
    $(() => {
        $('#add-enemy, #add-enemy a').click((e) => {
            e.preventDefault();
            e.stopPropagation();
            $('#ninja-enemy').removeClass('hidden');
        });
    });
};

$(() => {
    const logResult = (result) => {
        logger().log(result);
        alert(JSON.stringify(result));
    };
    const showTarget = (target) => {
        logger().log(target);
    };
    const offset = 2;
    // api.nextTarget({ offset }).then((res) => res.json()).then(logResult);
    api.nextTarget({ offset }).then((res) => res.json()).then((data) => {
        logger().log(data);
        const { id, uname: name, health, maxHealth, level, xp, maxXp, attack, defense, speed, strength, stamina, image } = data;
        logger().log(name, health, strength, speed, stamina);
    });

    // UI rendering
    render();
});
