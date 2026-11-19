# SeatFlow (실시간 스터디룸/좌석 예약 시스템)

바닐라 PHP와 MySQL(PDO)로 구현하는 실시간 스터디룸 및 좌석 예약 웹 애플리케이션입니다.

## 주요 기능 (예정 및 진행)

- **룸 및 좌석 현황 조회**: 스터디룸/열람실(`rooms`)과 좌석(`seats`) 상태(`active`, `maintenance` 등) 확인
- **회원가입 및 로그인**: 세션 기반 인증 및 비밀번호 해싱(`password_hash` / `password_verify`)
- **실시간 좌석 예약 및 중복 방지**: 시간대 겹침 검증 및 트랜잭션 처리(`reservations`)

## 기술 스택

- **Backend**: Vanilla PHP (PHP 8.x), PDO (`pdo_mysql`)
- **Database**: MySQL (`seat_flow`)
- **Frontend**: HTML, CSS, JavaScript
- **Local Server**: PHP Built-in Server (`php -S localhost:8000 -t public`)

## 로컬 실행 방법

1. MySQL에서 `seat_flow` 데이터베이스 생성 및 테이블(`users`, `rooms`, `seats`, `reservations`) 구성
2. 루트 경로의 `.env` 파일에 DB 접속 정보 설정
3. 프로젝트 루트에서 PHP 내장 서버 실행
   ```bash
   php -S localhost:8000 -t public
   ```
