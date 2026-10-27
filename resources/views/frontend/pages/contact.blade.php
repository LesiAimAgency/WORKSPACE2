@extends('frontend.layouts.app')

@section('content')
<main class="p-subpage p-contact">
    <section class="contact">
        <div class="l-container">
            <div class="l-sidebar">
                <div class="contact__grid">
                    <div class="contact__col contact__col--left">
                        <div class="contact__support">
                            <div class="l-sidebar__ttl">Bạn cần hỗ trợ?</div>
                            <div class="contact__text">
                                <b>{{ setting('site_name', 'Company') }}</b> rất hân hạnh được hỗ trợ bạn, hãy để lại thông tin cho chúng tôi nhé.
                            </div>
                        </div>
                        <div id="pagelogin" class="contact__form">
                            <form method="post" action="{{ route('contact.submit') }}">
                                @csrf
                                <div class="contact-form">
                                    <div class="contact-form__row">
                                        <div class="contact-form__field contact-form__field--half">
                                            <label class="contact-form__label">Họ và tên <em>*</em></label>
                                            <input placeholder="Tên đầy đủ" type="text" class="contact-form__input" required name="name">
                                        </div>
                                        <div class="contact-form__field contact-form__field--half">
                                            <label class="contact-form__label">Email <em>*</em></label>
                                            <input placeholder="Địa chỉ email" type="email" required class="contact-form__input" name="email">
                                        </div>
                                    </div>
                                    <fieldset class="contact-form__field">
                                        <label class="contact-form__label">Tin nhắn<em>*</em></label>
                                        <textarea placeholder="Đừng ngại hỏi về đơn hàng của bạn" name="message" class="contact-form__textarea" rows="5" required></textarea>
                                    </fieldset>
                                    <div class="contact-form__actions">
                                        <button type="submit" class="contact-form__submit c-btn01 c-btn01--fw400">Gửi</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="contact__col contact__col--right">
                        <div class="contact-info contact-info--about">
                            <div class="l-sidebar__ttl">{{ setting('company_name', 'CÔNG TY TNHH') }}</div>
                            <div class="contact-info__content">
                                <ul class="contact-info__list">
                                    <li class="contact-info__item">{!! setting('company_description') !!}</li>
                                    <li class="contact-info__item">{{ setting('company_address') }}</li>
                                    <li class="contact-info__item">{{ setting('company_registration') }}</li>
                                </ul>
                            </div>
                        </div>
                        <div class="contact-info">
                            <div class="contact-info__title">Liên hệ với chúng tôi</div>
                            <ul class="contact-info__list">
                                <li class="contact-info__item">Email: <a class="contact-info__link" href="mailto:{{ setting('contact_email') }}">{{ setting('contact_email') }}</a></li>
                                <li class="contact-info__item">CSKH: <a class="contact-info__link" href="tel:{{ setting('contact_phone') }}">{{ setting('contact_phone') }}</a></li>
                                <li class="contact-info__item">{{ setting('working_hours') }}</li>
                                <li class="contact-info__item">Fanpage: <a class="contact-info__link" href="{{ setting('facebook_url') }}">{{ setting('facebook_name') }}</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>
@endsection
