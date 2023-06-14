import moment from 'moment/min/moment-with-locales.min'

const locale = document.querySelector('html').getAttribute('lang')

export function fromNow(date) {
    return moment(date).locale(locale).fromNow()
}

export function formatDate(date, format = 'LLLL') {
    return moment(date).locale(locale).format(format)
}
