# Page snapshot

```yaml
- generic [ref=e1]:
  - main [ref=e4]:
    - generic [ref=e6]:
      - generic [ref=e7]:
        - generic [ref=e8]: Savings Manager
        - heading "Sign in" [level=1] [ref=e9]
        - paragraph [ref=e10]:
          - text: or
          - link "sign up for an account" [ref=e11] [cursor=pointer]:
            - /url: http://localhost:8000/admin/register
      - generic [ref=e15]:
        - generic [ref=e19]:
          - generic [ref=e22]:
            - generic [ref=e26]:
              - text: Email address
              - superscript [ref=e27]: "*"
            - textbox "Email address*" [active] [ref=e31]
          - generic [ref=e34]:
            - generic [ref=e38]:
              - text: Password
              - superscript [ref=e39]: "*"
            - generic [ref=e41]:
              - textbox "Password*" [ref=e43]
              - button "Show password" [ref=e46] [cursor=pointer]:
                - img [ref=e47]
          - generic [ref=e55]:
            - checkbox "Remember me" [ref=e56]
            - generic [ref=e57]: Remember me
        - button "Sign in" [ref=e63] [cursor=pointer]:
          - generic [ref=e64]: Sign in
  - generic:
    - status
```