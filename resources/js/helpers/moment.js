import moment from 'moment/min/moment-with-locales.min'

const locale = document.querySelector('html').getAttribute('lang')

export function fromNow(date) {
    return moment(date).locale(locale).fromNow()
}

export function formatDate(date, format = 'LLLL') {
    return moment(date).locale(locale).format(format)
}

export function toDate(date) {
    // TODO moment.js?t=1721142607202:10 Deprecation warning: value provided is not in a recognized RFC2822 or ISO format
    return moment(date, 'DD/MM/YYYY HH:mm').toDate()
}
