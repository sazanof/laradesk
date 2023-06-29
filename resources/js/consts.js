export const TYPE_TEXT = 'TEXT'
export const TYPE_TEXTAREA = 'TEXTAREA'
export const TYPE_RICHTEXT = 'RICHTEXT'
export const TYPE_DROPDOWN = 'DROPDOWN'
export const TYPE_CHECKBOX = 'CHECKBOX'
export const TYPE_RADIO = 'RADIO'
export const TYPE_FILE = 'FILE'
export const TYPES = {
    TYPE_TEXT,
    TYPE_TEXTAREA,
    TYPE_RICHTEXT,
    TYPE_DROPDOWN,
    TYPE_CHECKBOX,
    TYPE_RADIO,
    TYPE_FILE
}
export const STATUSES = {
    NEW: 1,
    IN_WORK: 2,
    WAITING: 3,
    SOLVED: 4,
    CLOSED: 5,
    IN_APPROVAL: 6,
    APPROVED: 7
}

export const COMMENT = {
    COMMENT: 1,
    SOLVED_COMMENT: 2,
    CLOSE_COMMENT: 3,
    APPROVE_COMMENT: 4,
    DECLINE_COMMENT: 5,
    REOPEN_COMMENT: 6
}


export const PARTICIPANT = {
    REQUESTER: 1,
    ASSIGNEE: 2,
    APPROVAL: 3,
    OBSERVER: 4
}
