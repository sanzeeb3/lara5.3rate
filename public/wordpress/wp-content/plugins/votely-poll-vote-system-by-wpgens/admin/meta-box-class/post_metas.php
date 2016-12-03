<?php

$fields = array(
    array(
        'id'    => '_gens_votely_active',
        'label' => 'Activate Votely on this post',
        'desc'  => 'Votely wont start unless this is on. Select "No" to hide Votely later.',
        'type'  => 'select',
        'options' => array (
            'no' => array (
                'label' => 'No',
                'value' => 'no'
            ),
            'yes' => array (
                'label' => 'Yes',
                'value' => 'yes'
            ),
        )
    ),
    array(
        'id'    => '_gens_votely_question',
        'label' => 'Votely Question',
        'type'  => 'text',
        'desc'  => 'Keep the question short if you want more engagements.',
    ),
    array(
        'id'    => '_gens_votely_first_answer',
        'label' => 'First Answer',
        'type'  => 'text',
        'desc'  => 'Yes or No, or something short that can fit 100px circle/square.',
    ),
    array(
        'id'    => '_gens_votely_second_answer',
        'label' => 'Second Answer',
        'type'  => 'text',
        'desc'  => 'Yes or No, or something short that can fit 100px circle/square.',
    ),
    array(
        'id'    => '_gens_votely_vote_active',
        'label' => 'Is Vote Active?',
        'desc'  => 'If not, users wont be able to vote, just see results.',
        'type'  => 'select',
        'options' => array (
            'yes' => array (
                'label' => 'Yes',
                'value' => 'yes'
            ),
            'no' => array (
                'label' => 'No',
                'value' => 'no'
            ),
        )
    ),
);
