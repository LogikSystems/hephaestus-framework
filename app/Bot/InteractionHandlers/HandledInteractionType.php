<?php

namespace App\Bot\InteractionHandlers;

use Discord\InteractionType;

enum HandledInteractionType: int {
    case APPLICATION_COMMAND                = InteractionType::APPLICATION_COMMAND;
    case APPLICATION_COMMAND_AUTOCOMPLETE   = InteractionType::APPLICATION_COMMAND_AUTOCOMPLETE;
    case MESSAGE_COMPONENT                  = InteractionType::MESSAGE_COMPONENT;
    case MODAL_SUBMIT                       = InteractionType::MODAL_SUBMIT;
    case PING                               = InteractionType::PING;
}
