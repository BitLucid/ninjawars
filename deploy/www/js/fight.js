/* eslint-disable max-len */
/* eslint-disable import/extensions */
/* eslint-disable indent */
/* global $ */

import api from './api.js';
import { logger, urlParam } from './utils.js';

// State handling for the current place
const [getOffset, setOffset] = [
    () => ((localStorage && JSON.parse(localStorage.getItem('offset'))) || 0),
    (off) => (localStorage && localStorage.setItem('offset', JSON.stringify(Math.max(0, off)))),
];

const completeLoading = () => {
    $('.skeleton').addClass('off').removeClass('skeleton');
};

const placeTarget = (target) => {
    const classList = ['Crane', 'Dragon', 'Viper', 'Tiger'];
    const {
        // eslint-disable-next-line camelcase
        id, uname: name, health, max_health, level, class_name, avatar_url, difficulty, status_list,
    } = target;
    // eslint-disable-next-line camelcase
    const healthPercent = Math.max(0, Math.min(100, Math.round((health / max_health) * 100)));
    const areas = {
        'char-target-name': name,
        'char-numeric-health': health,
        'char-level': level,
        // eslint-disable-next-line camelcase
        'char-class': class_name,
        // eslint-disable-next-line camelcase
        'char-avatar': avatar_url,
        'char-difficulty': difficulty,
        // eslint-disable-next-line camelcase
        'char-status': status_list,
    };
    Object.entries(areas).forEach(([keyName, info]) => {
        $(`.target-preview .${keyName}`).text(info);
    });
    $('.target-preview .char-class').removeClass(classList).addClass(areas['char-class']);
    $('.target-preview .char-avatar').attr('src', areas['char-avatar']);
    $('.target-preview .character-health-bar').css({ width: `${healthPercent}%` }).html('&nbsp;');
    $('.target-preview .char-status').removeClass(['Healthy', 'Dead']).addClass(areas['char-status']);
};

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
    const nextTarget = (offset = 0) => {
        logger().log('Getting next target', offset);
        return api.nextTarget(offset).then((res) => res.json()).then((data) => {
            placeTarget(data);
            logger().log('Target health: ', data.health, data);
            completeLoading();
            return data;
        });
    };
    // Set initial offset from url, fallback to 0
    setOffset(parseInt(urlParam('offset'), 10) || 0);
    nextTarget(getOffset());
    $('.target-container .spin-enemy.up').click(() => {
        setOffset(getOffset() + 1);
        nextTarget(getOffset());
    });
    $('.target-container .spin-enemy.down').click(() => {
        setOffset(getOffset() - 1);
        nextTarget(getOffset());
    });

    // UI rendering
    render();
});
